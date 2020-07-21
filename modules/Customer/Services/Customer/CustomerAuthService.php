<?php

namespace Modules\Customer\Services\Customer;

use Config;
use Carbon\Carbon;

use Modules\Customer\Models\Customer\Customer;

use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Customer\Repositories\Customer\CustomerRepository;
use Modules\Customer\Repositories\Customer\CustomerDetailRepository;

use Modules\Customer\Events\CustomerLoginEvent;

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
 * Class CustomerAuthService
 * 
 * @package App\Services\Customer
 */
class CustomerAuthService extends BaseService
{

    /**
     * @var  \App\Repositories\Customer\CustomerRepository
     */
    protected $customerrepository;


    /**
     * @var  \App\Repositories\Customer\CustomerDetailRepository
     */
    protected $customerdetailrepository;


    /**
     * Customer Service
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
     * @var \Modules\Customer\Models\Customer\Customer
     */
    protected $customer;


    /**
     * AuthService constructor.
     *
     * @param \App\Repositories\Organization\OrganizationRepository     $organizationrepository
     * @param \App\Repositories\Lookup\LookupValueRepository            $lookuprepository
     * @param \App\Repositories\Customer\CustomerRepository             $customerrepository
     * @param \App\Repositories\Customer\CustomerDetailRepository       $customerdetailrepository
     * 
     * @param \Illuminate\Auth\AuthManager                              $authManager
     * @param \Illuminate\Auth\Passwords\PasswordBrokerManager          $passwordBrokerManager
     * @param \Modules\Customer\Models\Customer\Customer                $customer
     */
    public function __construct(
        OrganizationRepository              $organizationrepository,
        LookupValueRepository               $lookuprepository,
        CustomerRepository                  $customerrepository,
        CustomerDetailRepository            $customerdetailrepository,
        CustomerService                     $customerservice,

        AuthManager                         $authManager, 
        PasswordBrokerManager               $passwordBrokerManager, 
        Customer                            $customer
    )
    {
        //Base service object
        $this->organizationrepository       = $organizationrepository;
        $this->lookuprepository             = $lookuprepository;

        //Customer service object
        $this->customerservice              = $customerservice;

        //Local service objects
        $this->customerrepository           = $customerrepository;
        $this->customerdetailrepository     = $customerdetailrepository;
        $this->authManager                  = $authManager;
        $this->passwordBrokerManager        = $passwordBrokerManager;
        $this->customer                     = $customer;
    }

    
    /**
     * Authenticate the Customer
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
            $customerDetails = $this->customerdetailrepository->getCustomerDetailsByIdentifier($organization['id'], $username, $type['id'], true, true);
            if (!empty($customerDetails)) {

                //Get the Customer
                $customer = $customerDetails->customer;
                if ($isSocialValidated) {
                    $token = $this->guard('frontend')->login($customer);
                } else {
                    if ($isOtpLogin) {
                        if (!empty($customer['last_otp']))
                        {
                            if ($customer['last_otp']==$credentials['password']) {

                            } else {
                                throw new AccessDeniedHttpException();
                            }
                        } else {
                            throw new AccessDeniedHttpException();
                        }
                    } else {
                        //Create authentication request
                        $credentials = [
                            'hash' => $customer['hash'],
                            'password' => $credentials['password'],
                            'is_active' => 1
                        ];

                        //Authenticate user
                        $token = $this->guard('frontend')->attempt($credentials);
                        if (empty($token))
                        {
                            throw new AccessDeniedHttpException();
                        } //End if

                        $customer = $this->guard('frontend')->user();
                    } //End if
                } //End if

                //Notify for Customer Authentication
                event(new CustomerLoginEvent($customer));

                //Attach token and params
                $customer['token_type'] = 'bearer';
                $customer['token'] = $token;
                $customer['created_on'] = time();
                $customer['expires_in'] = $this->guard('frontend')->factory()->getTTL() * 60;

                $objReturnValue=$customer;
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
     * Register the Customer for an Organization
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
            function (User $customer, $password) {
                $customer->password = $password;
                $customer->save();
                event(new PasswordReset($customer));
            }
        );

        return $response === Password::PASSWORD_RESET;

    }


    /**
     * Logout the Customer
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
            $customer = $this->getCurrentUser('frontend');

            //Logout the customer
            $objReturnValue = $this->guard('frontend')->logout();

        } catch(AccessDeniedHttpException $e) {
            throw new AccessDeniedHttpException();
        } catch(Exception $e) {
            throw new Exception($e);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Authenticate the Customer using Social Credentials
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
                $customer = $this->customerservice->create($orgHash, $payload, $ipAddress, $provider, 0, true);
            } else {
                //Authenticate Customer
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
