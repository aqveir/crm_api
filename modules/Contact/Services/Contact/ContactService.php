<?php

namespace Modules\Contact\Services\Contact;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Contact\Repositories\Contact\ContactRepository;
use Modules\Contact\Repositories\Contact\ContactDetailRepository;

use Modules\Contact\Events\ContactAddedEvent;

use Modules\Core\Services\BaseService;
use Modules\Contact\Notifications\ContactActivationNotification;

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

/**
 * Class ContactService
 * 
 * @package Modules\Contact\Services\Contact
 */
class ContactService extends BaseService
{
    /**
     * @var Modules\Core\Repositories\Lookup\LookupValueRepository
     */
    protected $lookuprepository;


    /**
     * @var Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationrepository;


    /**
     * @var Modules\Contact\Repositories\Contact\ContactRepository
     */
    protected $customerrepository;


    /**
     * @var Modules\Contact\Repositories\Contact\ContactDetailRepository
     */
    protected $customerdetailrepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookuprepository
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationrepository
     * @param \Modules\Contact\Repositories\Contact\ContactRepository        $customerrepository
     * @param \Modules\Contact\Repositories\Contact\ContactDetailRepository  $customerdetailrepository
     */
    public function __construct(
        LookupValueRepository               $lookuprepository,
        OrganizationRepository              $organizationrepository,
        ContactRepository                   $customerrepository,
        ContactDetailRepository             $customerdetailrepository
    ) {
        $this->lookuprepository             = $lookuprepository;
        $this->organizationrepository       = $organizationrepository;
        $this->customerrepository           = $customerrepository;
        $this->customerdetailrepository     = $customerdetailrepository;
    } //Function ends


    /**
     * Contact Exists
     * 
     * @param string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * 
     * @return bool
     */
    public function validateContactExists(string $orgHash, Collection $payload)
    {
        $objReturnValue=false;
        try {
            //Get organization data
            $organization = $this->organizationrepository->getOrganizationByHash($orgHash);

            $type_key = null;
            $data = null;
            if ($payload->has('email')) {
                $type_key = config('omnichannel.settings.static.key.lookup_value.email');
                $data = $payload['email'];
            } elseif ($payload->has('phone')) {
                $type_key = config('omnichannel.settings.static.key.lookup_value.phone');
                $data = $payload['phone'];
            } else {
                throw new Exception();
            } //End if

            //Check if the Contact exists
            $response = $this->customerdetailrepository->getContactDetailsByIdentifier($organization['id'], $data, null, true, true);

            $objReturnValue = !empty($response);
        } catch(ModelNotFoundException $e) {
            Log::error('ContactService:validateContactExists:ModelNotFoundException:' . $e->getMessage());
        } catch (Exception $e) {
            log::error('ContactService:validateContactExists:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Get All Contact Full Data (Backend)
     * 
     * @param \Illuminate\Support\Collection $payload
     * 
     * @return object
     */
    public function getFullData(string $orgHash, Collection $payload)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Forced params
            $isForcedFromDB = $this->isForced($payload);

            //Load Contact Data
            $objReturnValue = $this->customerrepository->getFullData($user['org_id'], $orgHash, $isForcedFromDB);

        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Get Contact Full Data By Hash Identifier (Backend)
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param string $hash (Customer Hash)
     * 
     * @return object
     */
    public function getFullDataByHash(string $orgHash, Collection $payload, string $hash)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Forced params
            $isForcedFromDB = $this->isForced($payload);

            //Load Contact Data
            $objReturnValue = $this->customerrepository->getFullDataByIdentifier($user['org_id'], $hash, $isForcedFromDB);

        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Get Contact Full Data By Hash Identifier (Frontend)
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return object
     */
    public function getContactFullData(string $orgHash, Request $request)
    {
        $objReturnValue=null;
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if(empty($organization)) {
                throw new AccessDeniedHttpException();
            } //End if

            //Authenticated User
            $contact = $this->getCurrentUser('frontend');

            log::info(json_encode($contact).'->'. $organization['id'].'->'.$contact['hash']);

            //Load Contact Data
            $objReturnValue = $this->customerrepository->getFullDataFromDB($organization['id'], $contact['hash']);

        } catch(AccessDeniedHttpException $e) {
            throw new AccessDeniedHttpException();
        } catch(UnauthorizedHttpException $e) {
            throw new UnauthorizedHttpException();
        } catch(Exception $e) {
            throw new Exception($e);
        } //Try-catch ends
        return $objReturnValue;
    } //Function ends


    /**
     * Function to return the phone number of a contact
     *
     * @return objReturnValue
     */
    public function getContactPhone(int $orgId=0, $contact, string $proxy=null, $isPrimary=null)
    {
        $objReturnValue = null;
        try {
            //Get Phone Type for the Organization
            $type = $this->lookuprepository->getLookUpByKey($orgId, config('omnichannel.settings.static.key.lookup_value.phone'));

            //Get phone details for a contact
            $isPrimary = ($proxy!=null)?null:true;
            $contactDetail = $this->customerdetailrepository->getContactDetailsByType($contact['id'], $type['id'], $isPrimary, $proxy);

            if($contactDetail!=null) {
                $country_code = '91'; //$contactDetail['country']['code'];
                $phone_number = $contactDetail['identifier'];
                $objReturnValue = '+'.$country_code.$phone_number;
            } else { throw new BadRequestHttpException(); } //End if-else
            
        } catch(Exception $e) {
            Log::error(json_encode($e));
            throw new NotFoundHttpException();
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Create New Contact
     * 
     * @param string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param string $ipAddress (optional)
     * @param string $provider (optional)
     * @param int $createdBy (optional)
     * @param bool $isEmailValid (optional)
     * @param bool $isPhoneValid (optional)
     * 
     */
    public function create(
        string $orgHash, Collection $payload, string $ipAddress='0.0.0.0',
        string $provider=null, int $createdBy=0,
        bool $isEmailValid=false, bool $isPhoneValid=false
    )
    {
        $objReturnValue=null;

        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Data for validation
            $dataValidate = ($payload->only(['email', 'phone']))->toArray();

            //Duplicate check
            $isDuplicate=$this->customerdetailrepository->validate($organization['id'], $dataValidate);
            if (!$isDuplicate) {

                //Generate the data payload to create user
                $payload = $payload->only('password', 'first_name', 'middle_name', 'last_name');
                $payload = array_merge(
                    $payload,
                    [
                        'org_id' => $organization['id'],
                        'last_otp' => null,
                        'group_id' => 0,
                        'created_by' => $createdBy,
                        'ip_address' => $ipAddress
                    ]
                );

                //Create Contact
                $contact = $this->customerrepository->create($payload);

                //Create Contact details - Email
                if(!empty($payload['email'])) {
                    //Get Email Type for the Organization
                    $type = $this->getLookupValueByKey($organization['id'], config('omnichannel.settings.static.key.lookup_value.email'));

                    $payloadDetails = [
                        'org_id' => $organization['id'],
                        'type_id' => (empty($type)?0:$type['id']),
                        'contact_id' => $contact['id'],
                        'identifier' => $payload['email'],
                        'is_primary' => 1,
                        'is_verified' => $isEmailValid,
                        'created_by'=> $createdBy
                    ];
                    $this->customerdetailrepository->create($payloadDetails);
                } //End if

                //Create Contact details - Phone
                if(!empty($payload['phone'])) {
                    //Get Phone Type for the Organization
                    $type = $this->getLookupValueByKey($organization['id'], config('omnichannel.settings.static.key.lookup_value.phone'));

                    $payloadDetails = [
                        'org_id' => $organization['id'],
                        'type_id' => (empty($type)?0:$type['id']),
                        'contact_id' => $contact['id'],
                        'identifier' => $payload['phone'],
                        'is_primary' => 1,
                        'is_verified' => $isPhoneValid,
                        'created_by'=> $createdBy
                    ];
                    $this->customerdetailrepository->create($payloadDetails);
                } //End if

                //Notify user to Activate Account
                //$contact->notify(new ContactActivationNotification($contact));

                //Raise event: New Contact Added
                event(new ContactAddedEvent($contact));

                $objReturnValue=$contact;
            } else {
                throw new DuplicateDataException();
            } //End if
        } catch(DuplicateDataException $e) {
            throw new DuplicateDataException();
        } catch(Exception $e) {
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends
