<?php

namespace Modules\ServiceRequest\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\ServiceRequest\Repositories\ServiceRequestRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Contact\Repositories\Contact\ContactRepository;
use Modules\Account\Repositories\AccountRepository;
use Modules\User\Repositories\User\UserRepository;

use Modules\Core\Services\BaseService;

use Modules\ServiceRequest\Events\ServiceRequest\ServiceRequestCreatedEvent;
use Modules\ServiceRequest\Events\ServiceRequest\ServiceRequestUpdatedEvent;
use Modules\ServiceRequest\Events\ServiceRequest\ServiceRequestDeletedEvent;

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
 * Class ServiceRequestService
 * @package Modules\ServiceRequest\Services
 */
class ServiceRequestService extends BaseService
{

    /**
     * @var Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;


    /**
     * @var Modules\ServiceRequest\Repositories\ServiceRequestRepository
     */
    protected $servicerequestRepository;


    /**
     * @var Modules\Core\Repositories\Lookup\LookupValueRepository
     */
    protected $lookupRepository;


    /**
     * @var \Modules\Contact\Repositories\Contact\ContactRepository
     */
    protected $contactRepository;


    /**
     * @var \Modules\Account\Repositories\AccountRepository
     */
    protected $accountRepository;


    /**
     * @var \Modules\User\Repositories\User\UserRepository
     */
    protected $userRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\ServiceRequest\Repositories\ServiceRequestRepository     $servicerequestRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupRepository
     * @param \Modules\Contact\Repositories\Contact\ContactRepository           $contactRepository
     * @param \Modules\Account\Repositories\AccountRepository                   $accountRepository
     * @param \Modules\User\Repositories\User\UserRepository                    $userRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        ServiceRequestRepository        $servicerequestRepository,
        LookupValueRepository           $lookupRepository,
        ContactRepository               $contactRepository,
        AccountRepository               $accountRepository,
        UserRepository                  $userRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->servicerequestRepository = $servicerequestRepository;
        $this->lookupRepository         = $lookupRepository;
        $this->contactRepository        = $contactRepository;
        $this->accountRepository        = $accountRepository;
        $this->userRepository           = $userRepository;
    } //Function ends


    /**
     * Get All ServiceRequests Full Data for an Oraganization (Backend)
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

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if (empty($organization)) { throw new BadRequestHttpException(); } //End if

            //Forced params
            $isForcedFromDB = $this->isForced($payload);

            //Page number and size limit
            $page = ($payload->has('page'))?$payload['page']:1;
            $size = ($payload->has('size'))?$payload['size']:10;

            //Get Category Lookup data
            $typeCategoryId = $this->getLookupValueId($organization['id'], $payload, 'category_key');

            //Load Contact Data
            $objReturnValue = $this->servicerequestRepository
                ->getFullData($organization['id'], $typeCategoryId, $isForcedFromDB, $page, $size);

        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Fetch ServiceRequest Data for an Oraganization by Identifier
     * 
     * @param  \string $orgHash
     * @param  \Illuminate\Support\Collection $payload
     * @param  \string $srHash
     * 
     * @return mixed
     */
    public function show(string $orgHash, Collection $payload, string $srHash) {
        $objReturnValue = null;
        $orgId = 0; $userId = 0;

        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);         

            //Get request data
            $data = $payload->toArray();

            //Fetch record
            $response = $this->servicerequestRepository
                ->getFullDataByIdentifier($organization['id'], $srHash);

            $objReturnValue = $response;
        } catch(NotFoundHttpException $e) {
            log::error('UserService:show:NotFoundHttpException:' . $e->getMessage());
            throw new NotFoundHttpException();
        } catch(BadRequestHttpException $e) {
            log::error('UserService:show:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserService:show:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

    
    /**
     * Create ServiceRequest
     * 
     * @param  \string $orgHash
     * @param  \Illuminate\Support\Collection $payload
     * @param  \string $ipAddress (optional)
     * @param  \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function create(string $orgHash, Collection $payload, string $ipAddress=null, bool $isAutoCreated=false)
    {
        $objReturnValue=null;$data=[];
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if (empty($organization)) { throw new BadRequestHttpException(); } //End if
            $data['org_id'] = $organization['id']; 

            //Get Contact Information
            $contact = $this->contactRepository->getFullDataByIdentifier($organization['id'], $payload['contact_hash']);
            if (empty($contact)) { throw new BadRequestHttpException(); } //End if
            $data['contact_id'] = $contact['id']; 

            //Build data
            $data = array_merge($data, $this->buildData($organization, $payload, true));

            //Set Created by
            $createdBy=0;
            if (!$isAutoCreated) {
                //Authenticated User
                $user = $this->getCurrentUser('backend');

                $createdBy = $user['id'];
            } //End if

            //Create ServiceRequest
            $serviceRequest = $this->servicerequestRepository->create($data, $createdBy, $ipAddress);
                
            //Raise event: ServiceRequest Created
            event(new ServiceRequestCreatedEvent($serviceRequest, $isAutoCreated));                

            //Assign to the return value
            $objReturnValue = $serviceRequest;

        } catch(AccessDeniedHttpException $e) {
            log::error('ServiceRequestService:create:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('ServiceRequestService:create:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('ServiceRequestService:create:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update ServiceRequest
     * 
     * @param  \string $orgHash
     * @param  \Illuminate\Support\Collection $payload
     * @param  \string $srHash
     * @param  \string $ipAddress (optional)
     *
     * @return mixed
     */
    public function update(string $orgHash, Collection $payload, string $srHash, string $ipAddress=null)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if (empty($organization)) { throw new BadRequestHttpException(); } //End if

            //Build data
            $data = array_merge($data, $this->buildData($organization, $payload));

            //Update ServiceRequest (update updated_by and ip_address)
            $serviceRequest = $this->servicerequestRepository->update($srHash, 'hash', $data, $user['id'], $ipAddress);
                
            //Raise event: ServiceRequest Updated
            event(new ServiceRequestUpdatedEvent($serviceRequest));                

            //Assign to the return value
            $objReturnValue = $serviceRequest;

        } catch(AccessDeniedHttpException $e) {
            log::error('ServiceRequestService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('ServiceRequestService:update:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('ServiceRequestService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Delete ServiceRequest
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \string $srHash
     * @param \string $ipAddress (optional)
     *
     * @return mixed
     */
    public function delete(string $orgHash, Collection $payload, string $srHash, string $ipAddress=null)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Delete ServiceRequest
            $serviceRequest = $this->servicerequestRepository->delete($srHash, 'hash', $user['id'], $ipAddress);
                
            //Raise event: ServiceRequest Updated
            event(new ServiceRequestDeletedEvent($serviceRequest));                

            //Assign to the return value
            $objReturnValue = $serviceRequest;

        } catch(AccessDeniedHttpException $e) {
            log::error('ServiceRequestService:delete:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('ServiceRequestService:delete:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('ServiceRequestService:delete:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Build ServiceRequest Data
     * 
     * @return array
     */
    private function buildData($organization, Collection $payload, bool $setPoolUser=false)
    {
        $objReturnValue=null;
        try {
            //Set ACCOUNT details by identifier
            $account = null;
            if ($payload->has('account_hash') && !empty($payload['account_hash'])) {
                $account = $this->accountRepository->getDataByHash($organization['id'], $payload['account_hash']);
            } else {
                $account = $this->accountRepository->getDefault($organization['id']);
            } //End if
            if (empty($account)) { throw new BadRequestHttpException(); } //End if
            $objReturnValue['account_id'] = $account['id']; 

            //Set OWNER User by Hash
            if ($payload->has('owner_hash') && !empty($payload['owner_hash'])) {
                $ownerUser = $this->userRepository->getDataByHash($organization['id'], $payload['owner_hash']);
                if (empty($ownerUser)) { throw new BadRequestHttpException(); } //End if
                $objReturnValue['owner_id'] = $ownerUser['id'];
            } else {
                //Set Pool User
                if ($setPoolUser) {
                    $poolUser = $this->userRepository->getPoolUser($organization['id']);
                    if (!empty($poolUser)) {
                        $objReturnValue['owner_id'] = $poolUser['id'];
                    } //End if
                } //End if
            } //End if

            //Lookup CATEGORY data
            $objReturnValue['category_id'] = $this->processLookup($organization['id'], $payload, 'category_key', 'service_request_category_lead');

            //Lookup TYPE data
            $objReturnValue['type_id'] = $this->processLookup($organization['id'], $payload, 'type_key', 'service_request_type_default');

            //Lookup STATUS data
            $objReturnValue['status_id'] = $this->processLookup($organization['id'], $payload, 'status_key', 'service_request_status_new');

            //Lookup STAGE data
            $objReturnValue['stage_id'] = $this->processLookup($organization['id'], $payload, 'stage_key', 'service_request_stage_new');

        } catch(Exception $e) {
            log::error('ServiceRequestService:buildData:Exception:' . $e->getMessage());
            throw $e;
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Process Lookup Data
     * 
     * @return int
     */
    private function processLookup(int $orgId, Collection $payload, string $key, string $defaultKey=null)
    {
        $objReturnValue=null;
        try {

            $lookupKey = ($payload->has($key) && (!empty($payload[$key])))?$payload[$key]:$defaultKey;

            //Check if the lookup key exists
            if (!empty($lookupKey)) {
                //Get lookup data
                $lookupData = $this->lookupRepository->getLookUpByKey($orgId, $lookupKey);
                if (empty($lookupData)) { throw new BadRequestHttpException(); } //End if
                $objReturnValue = $lookupData['id'];
            } //End if
        } catch(Exception $e) {
            log::error('ServiceRequestService:processLookup:Exception:' . $e->getMessage());
            throw $e;
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends