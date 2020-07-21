<?php

namespace Modules\Customer\Services\Customer;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Customer\Repositories\Customer\CustomerRepository;
use Modules\Customer\Repositories\Customer\CustomerDetailRepository;

use Modules\Customer\Events\CustomerAddedEvent;

use Modules\Core\Services\BaseService;
use Modules\Customer\Notifications\CustomerActivationNotification;

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
 * Class CustomerService
 * 
 * @package Modules\Customer\Services\Customer
 */
class CustomerService extends BaseService
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
     * @var Modules\Customer\Repositories\Customer\CustomerRepository
     */
    protected $customerrepository;


    /**
     * @var Modules\Customer\Repositories\Customer\CustomerDetailRepository
     */
    protected $customerdetailrepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookuprepository
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationrepository
     * @param \Modules\Customer\Repositories\Customer\CustomerRepository        $customerrepository
     * @param \Modules\Customer\Repositories\Customer\CustomerDetailRepository  $customerdetailrepository
     */
    public function __construct(
        LookupValueRepository               $lookuprepository,
        OrganizationRepository              $organizationrepository,
        CustomerRepository                  $customerrepository,
        CustomerDetailRepository            $customerdetailrepository
    ) {
        $this->lookuprepository             = $lookuprepository;
        $this->organizationrepository       = $organizationrepository;
        $this->customerrepository           = $customerrepository;
        $this->customerdetailrepository     = $customerdetailrepository;
    } //Function ends


    /**
     * Customer Exists
     * 
     * @param string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * 
     * @return bool
     */
    public function validateCustomerExists(string $orgHash, Collection $payload)
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

            //Check if the Customer exists
            $response = $this->customerdetailrepository->getCustomerDetailsByIdentifier($organization['id'], $data, null, true, true);

            $objReturnValue = !empty($response);
        } catch(ModelNotFoundException $e) {
            Log::error('CustomerService:validateCustomerExists:ModelNotFoundException:' . $e->getMessage());
        } catch (Exception $e) {
            log::error('CustomerService:validateCustomerExists:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Get Customer Full Data By Hash Identifier (Backend)
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $hash
     * 
     * @return object
     */
    public function getFullData(Request $request, string $hash)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Load Customer Data
            $objReturnValue = $this->customerrepository->getFullDataFromDB($user['org_id'], $hash);

        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Get Customer Full Data By Hash Identifier (Frontend)
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return object
     */
    public function getCustomerFullData(string $orgHash, Request $request)
    {
        $objReturnValue=null;
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if(empty($organization)) {
                throw new AccessDeniedHttpException();
            } //End if

            //Authenticated User
            $customer = $this->getCurrentUser('frontend');

            log::info(json_encode($customer).'->'. $organization['id'].'->'.$customer['hash']);

            //Load Customer Data
            $objReturnValue = $this->customerrepository->getFullDataFromDB($organization['id'], $customer['hash']);

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
     * Function to return the phone number of a customer
     *
     * @return objReturnValue
     */
    public function getCustomerPhone(int $orgId=0, $customer, string $proxy=null, $isPrimary=null)
    {
        $objReturnValue = null;
        try {
            //Get Phone Type for the Organization
            $type = $this->lookuprepository->getLookUpByKey($orgId, config('omnichannel.settings.static.key.lookup_value.phone'));

            //Get phone details for a customer
            $isPrimary = ($proxy!=null)?null:true;
            $contactDetail = $this->customerdetailrepository->getCustomerDetailsByType($customer['id'], $type['id'], $isPrimary, $proxy);

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
     * Create New Customer
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

                //Create Customer
                $customer = $this->customerrepository->create($payload);

                //Create Customer details - Email
                if(!empty($payload['email'])) {
                    //Get Email Type for the Organization
                    $type = $this->getLookupValueByKey($organization['id'], config('omnichannel.settings.static.key.lookup_value.email'));

                    $payloadDetails = [
                        'org_id' => $organization['id'],
                        'type_id' => (empty($type)?0:$type['id']),
                        'customer_id' => $customer['id'],
                        'identifier' => $payload['email'],
                        'is_primary' => 1,
                        'is_verified' => $isEmailValid,
                        'created_by'=> $createdBy
                    ];
                    $this->customerdetailrepository->create($payloadDetails);
                } //End if

                //Create Customer details - Phone
                if(!empty($payload['phone'])) {
                    //Get Phone Type for the Organization
                    $type = $this->getLookupValueByKey($organization['id'], config('omnichannel.settings.static.key.lookup_value.phone'));

                    $payloadDetails = [
                        'org_id' => $organization['id'],
                        'type_id' => (empty($type)?0:$type['id']),
                        'customer_id' => $customer['id'],
                        'identifier' => $payload['phone'],
                        'is_primary' => 1,
                        'is_verified' => $isPhoneValid,
                        'created_by'=> $createdBy
                    ];
                    $this->customerdetailrepository->create($payloadDetails);
                } //End if

                //Notify user to Activate Account
                //$customer->notify(new CustomerActivationNotification($customer));

                //Raise event: New Customer Added
                event(new CustomerAddedEvent($customer));

                $objReturnValue=$customer;
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
