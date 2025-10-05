<?php

namespace Modules\Contact\Services\Contact;

use Config;
use Carbon\Carbon;

use Modules\Contact\Models\Contact\Contact;

use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Contact\Repositories\Contact\ContactRepository;
use Modules\Contact\Repositories\Contact\ContactDetailRepository;

use Modules\Contact\Events\ContactLoginEvent;

use Modules\Core\Services\BaseService;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use Exception;
use Modules\Core\Exceptions\DuplicateDataException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Support\Facades\Password;

/**
 * Class ContactAuthService
 * 
 * @package App\Services\Contact
 */
class ContactAuthService extends BaseService
{

    /**
     * @var  \App\Repositories\Contact\ContactRepository
     */
    protected $customerrepository;


    /**
     * @var  \App\Repositories\Contact\ContactDetailRepository
     */
    protected $customerdetailrepository;


    /**
     * Contact Service
     */
    protected $customerservice;


    /**
     * @var \Illuminate\Auth\AuthManager
     */
    protected $authManager;


    /**
     * @var \Illuminate\Auth\Passwords\PasswordBrokerManager
     */
    protected $passwordBrokerManager;


    /**
     * @var \Modules\Contact\Models\Contact\Contact
     */
    protected $contact;


    /**
     * AuthService constructor.
     *
     * @param \App\Repositories\Organization\OrganizationRepository     $organizationrepository
     * @param \App\Repositories\Lookup\LookupValueRepository            $lookuprepository
     * @param \App\Repositories\Contact\ContactRepository             $customerrepository
     * @param \App\Repositories\Contact\ContactDetailRepository       $customerdetailrepository
     * 
     * @param \Illuminate\Auth\AuthManager                              $authManager
     * @param \Illuminate\Auth\Passwords\PasswordBrokerManager          $passwordBrokerManager
     * @param \Modules\Contact\Models\Contact\Contact                $contact
     */
    public function __construct(
        OrganizationRepository              $organizationrepository,
        LookupValueRepository               $lookuprepository,
        ContactRepository                  $customerrepository,
        ContactDetailRepository            $customerdetailrepository,
        ContactService                     $customerservice,

        AuthManager                         $authManager, 
        PasswordBrokerManager               $passwordBrokerManager, 
        Contact                            $contact
    )
    {
        //Base service object
        $this->organizationrepository       = $organizationrepository;
        $this->lookuprepository             = $lookuprepository;

        //Contact service object
        $this->customerservice              = $customerservice;

        //Local service objects
        $this->customerrepository           = $customerrepository;
        $this->customerdetailrepository     = $customerdetailrepository;
        $this->authManager                  = $authManager;
        $this->passwordBrokerManager        = $passwordBrokerManager;
        $this->contact                     = $contact;
    }

    
    /**
     * Authenticate the Contact
     * 
     * @param string $orgHash
     * @param \Illuminate\Support\Collection $credentials
     * @param string $ipAddress (optional)
     * @param bool $isSocialValidated (optional)
     *
     * @return bool
     */
    public function authenticate(string $orgHash, Collection $credentials, string $ipAddress='0.0.0.0', bool $isSocialValidated=false)
    {
        $objReturnValue=null;
        $token = null;

        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if(empty($organization)) {
                throw new AccessDeniedHttpException();
            } //End if

            // Set username and type object
            $isOtpLogin = false;
            $type = null;
            $username = $credentials['username'];
            if (is_numeric($username)) {
                $isOtpLogin = true;
                $type=$this->getLookupValueByKey($organization['id'], config('omnichannel.settings.static.key.lookup_value.phone'));
            } else {
                $type=$this->getLookupValueByKey($organization['id'], config('omnichannel.settings.static.key.lookup_value.email'));
            } //End if

            //Check User details exit for the user identifier
            $customerDetails = $this->customerdetailrepository->getContactDetailByIdentifier($organization['id'], $username, $type['id'], true, true);
            if (!empty($customerDetails)) {

                //Get the Contact
                $contact = $customerDetails->contact;
                if ($isSocialValidated) {
                    $token = $this->guard('frontend')->login($contact);
                } else {
                    if ($isOtpLogin) {
                        if (!empty($contact['mfa_secret']))
                        {
                            if ($contact['mfa_secret']==$credentials['password']) {

                            } else {
                                throw new AccessDeniedHttpException();
                            }
                        } else {
                            throw new AccessDeniedHttpException();
                        }
                    } else {
                        //Create authentication request
                        $credentials = [
                            'hash' => $contact['hash'],
                            'password' => $credentials['password'],
                            'is_active' => 1
                        ];

                        //Authenticate user
                        $token = $this->guard('frontend')->attempt($credentials);
                        if (empty($token))
                        {
                            throw new AccessDeniedHttpException();
                        } //End if

                        $contact = $this->guard('frontend')->user();
                    } //End if
                } //End if

                //Notify for Contact Authentication
                event(new ContactLoginEvent($contact));

                //Attach token and other params
                $contact['token_type'] = 'bearer';
                $contact['token'] = $token;
                $contact['created_on'] = time();
                $contact['expires_in'] = $this->guard('frontend')->factory()->getTTL() * 60;

                $objReturnValue=$contact;
            } else {
                throw new AccessDeniedHttpException();
            } //End if

        } catch(AccessDeniedHttpException $e) {
            throw new AccessDeniedHttpException();
        } catch(Exception $e) {
            throw new Exception($e);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Register the Contact for an Organization
     * 
     * @param string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param string $ipAddress (optional)
     *
     * @return mixed
     */
    public function register(string $orgHash, Collection $payload, string $ipAddress='0.0.0.0')
    {
        return $this->customerservice->create($orgHash, $payload, $ipAddress);
    } //Function ends


    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public function sendForgotPasswordResetLink(Request $request)
    {
        $response = $this->passwordBrokerManager->sendResetLink(
            $request->only('email')
        );

        return $response === Password::RESET_LINK_SENT;
    }


    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public function resetPassword(Request $request)
    {
        $response = $this->passwordBrokerManager->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $contact, $password) {
                $contact->password = $password;
                $contact->save();
                event(new PasswordReset($contact));
            }
        );

        return $response === Password::PASSWORD_RESET;

    }


    /**
     * Logout the Contact
     * 
     * @param string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param string $ipAddress (optional)
     *
     * @return mixed
     */
    public function logout(string $orgHash, Collection $payload, string $ipAddress='0.0.0.0')
    {
        $objReturnValue=null;
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if (empty($organization)) {
                throw new AccessDeniedHttpException();
            } //End if

            //Authenticated User
            $contact = $this->getCurrentUser('frontend');

            //Logout the contact
            $objReturnValue = $this->guard('frontend')->logout();

        } catch(AccessDeniedHttpException $e) {
            throw new AccessDeniedHttpException();
        } catch(Exception $e) {
            throw new Exception($e);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Authenticate the Contact using Social Credentials
     * 
     * @param string $orgHash
     * @param string $provider
     * @param \Illuminate\Support\Collection $payload
     * @param string $ipAddress (optional)
     *
     * @return mixed
     */
    public function socialAuthenticate(string $orgHash, string $provider, $payload, string $ipAddress='0.0.0.0') {
        $objReturnValue=null;

        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Data for validation
            $dataValidate = ($payload->only(['email', 'phone']))->toArray();

            //Duplicate check
            $isDuplicate=$this->customerdetailrepository->validate($organization['id'], $dataValidate);
            if (!$isDuplicate) {
                //New Registration
                $contact = $this->customerservice->create($orgHash, $payload, $ipAddress, $provider, 0, true);
            } else {
                //Authenticate Contact
                $objReturnValue = $this->authenticate($orgHash, $payload, $ipAddress, true);
            } //End if
        } catch(DuplicateDataException $e) {
            throw new DuplicateDataException();
        } catch(Exception $e) {
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends
