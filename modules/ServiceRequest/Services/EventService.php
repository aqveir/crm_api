<?php

namespace Modules\ServiceRequest\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\ServiceRequest\Repositories\ServiceRequestRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\User\Repositories\User\UserRepository;
use Modules\ServiceRequest\Repositories\EventRepository as ServiceRequestEventRepository;

use Modules\Core\Services\BaseService;

use Modules\ServiceRequest\Events\ServiceRequestEvent\EventCreated;
use Modules\ServiceRequest\Events\ServiceRequestEvent\EventUpdated;
use Modules\ServiceRequest\Events\ServiceRequestEvent\EventDeleted;

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
 * Class EventService
 * 
 * @package Modules\ServiceRequest\Services
 */
class EventService extends BaseService
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
     * @var \Modules\User\Repositories\User\UserRepository
     */
    protected $userRepository;


    /**
     * @var \Modules\ServiceRequest\Repositories\EventRepository as ServiceRequestEventRepository
     */
    protected $eventRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\ServiceRequest\Repositories\ServiceRequestRepository     $servicerequestRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupRepository
     * @param \Modules\User\Repositories\User\UserRepository                    $userRepository
     * @param \ServiceRequestEventRepository                                    $eventRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        ServiceRequestRepository        $servicerequestRepository,
        LookupValueRepository           $lookupRepository,
        UserRepository                  $userRepository,
        ServiceRequestEventRepository   $eventRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->servicerequestRepository = $servicerequestRepository;
        $this->lookupRepository         = $lookupRepository;
        $this->userRepository           = $userRepository;
        $this->eventRepository          = $eventRepository;
    } //Function ends


    /**
     * Get All Event Data for an Oraganization (Backend)
     * 
     * @para  \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * 
     * @return object
     */
    public function getAll(string $orgHash, Collection $payload)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get Organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if (empty($organization)) { throw new BadRequestHttpException(); } //End if

            //Forced params
            $isForcedFromDB = $this->isForced($payload);

            //Page number and size limit
            $page = ($payload->has('page'))?$payload['page']:1;
            $size = ($payload->has('size'))?$payload['size']:10;

            //Get Activity Type: Event Lookup data
            $typeEvent = $this->lookupRepository->getLookUpByKey($organization['id'], 'service_request_activity_type_event');

            //Load Event Data
            $objReturnValue = $this->eventRepository
                ->getFullData($organization['id'], $typeEvent['id'], $isForcedFromDB, $page, $size);

        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Create ServiceRequestEvent
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function create(string $orgHash, string $srHash, Collection $payload, string $ipAddress=null, bool $isAutoCreated=false)
    {
        $objReturnValue=null; $data=[];
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if (empty($organization)) { throw new BadRequestHttpException(); } //End if

            //Build data
            $data = $payload->only(['subject', 'description', 'scheduled_at'])->toArray();

            if ($isAutoCreated) {
                //Build data
                $data = array_merge($data, [
                    'created_by' => 0,
                    'ip_address' => $ipAddress
                ]);
            } else {
                //Authenticated User
                $user = $this->getCurrentUser('backend');

                //Build data
                $data = array_merge($data, [
                    'created_by' => $user['id'] ,
                    'ip_address' => $ipAddress
                ]);
            } //End if

            //Set Organization data
            $data['org_id'] = $organization['id'];

            //Get ServiceRequest details by identifier
            $serviceRequest = $this->servicerequestRepository->getFullDataByIdentifier($organization['id'], $srHash);
            if (empty($serviceRequest)) { throw new BadRequestHttpException(); } //End if
            $data['servicerequest_id'] = $serviceRequest['id'];

            //Get Assignee User by Hash
            $assigneeUser = $this->userRepository->getDataByHash($organization['id'], $payload['assignee_uHash']);
            if (empty($assigneeUser)) { throw new BadRequestHttpException(); } //End if
            $data['user_id'] = $assigneeUser['id'];

            //Lookup Type data
            $lookupType = $this->lookupRepository->getLookUpByKey($organization['id'], 'service_request_activity_type_task');
            if (empty($lookupType)) { throw new BadRequestHttpException(); } //End if
            $data['type_id'] = $lookupType['id'];

            //Lookup SubType data
            $lookupSubType = $this->lookupRepository->getLookUpByKey($organization['id'], $payload['subtype_key']);
            if (empty($lookupSubType)) { throw new BadRequestHttpException(); } //End if
            $data['subtype_id'] = $lookupSubType['id'];

            //Lookup ServiceRequestEvent Priority data
            $lookupPriority = $this->lookupRepository->getLookUpByKey($organization['id'], $payload['priority_key']);
            if (empty($lookupPriority)) { throw new BadRequestHttpException(); } //End if
            $data['priority_id'] = $lookupPriority['id'];

            //Create ServiceRequestEvent
            $task = $this->eventRepository->create($data);
            $task->load('subtype', 'priority', 'assignee', 'owner');
                
            //Raise event: ServiceRequestEvent Created
            event(new TaskCreatedEvent($task, $isAutoCreated));                

            //Assign to the return value
            $objReturnValue = $task;

        } catch(AccessDeniedHttpException $e) {
            log::error('EventService:create:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('EventService:create:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('EventService:create:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update ServiceRequestEvent
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \int $eventId
     *
     * @return mixed
     */
    public function update(string $orgHash, string $srHash, Collection $payload, int $eventId, string $ipAddress=null)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if (empty($organization)) { throw new BadRequestHttpException(); } //End if

            //Build data
            $data = $payload->only(['subject', 'description', 'scheduled_at', 'completed_at'])->toArray();

            //Get Assignee User by Hash
            $assigneeUser = $this->userRepository->getDataByHash($organization['id'], $payload['assignee_uHash']);
            if (empty($assigneeUser)) { throw new BadRequestHttpException(); } //End if
            $data['user_id'] = $assigneeUser['id'];

            //Lookup SubType data
            $lookupSubType = $this->lookupRepository->getLookUpByKey($organization['id'], $payload['subtype_key']);
            if (empty($lookupSubType)) { throw new BadRequestHttpException(); } //End if
            $data['subtype_id'] = $lookupSubType['id'];

            //Lookup ServiceRequestEvent Priority data
            $lookupPriority = $this->lookupRepository->getLookUpByKey($organization['id'], $payload['priority_key']);
            if (empty($lookupPriority)) { throw new BadRequestHttpException(); } //End if
            $data['priority_id'] = $lookupPriority['id'];

            //Update IP address
            $data['ip_address'] = $ipAddress;

            //Update ServiceRequestEvent
            $task = $this->eventRepository->update($eventId, 'id', $data, $user['id']);
                
            //Raise event: ServiceRequestEvent Updated
            event(new TaskUpdatedEvent($task));                

            //Assign to the return value
            $objReturnValue = $task;

        } catch(AccessDeniedHttpException $e) {
            log::error('EventService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('EventService:update:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('EventService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Delete ServiceRequestEvent
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \int $eventId
     *
     * @return mixed
     */
    public function delete(string $orgHash, string $srHash, Collection $payload, int $eventId, string $ipAddress=null)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Delete ServiceRequestEvent
            $eventServiceRequest = $this->eventRepository->delete($eventId, 'id', $user['id'], $ipAddress);
            if ($eventServiceRequest) {
                //Raise event: ServiceRequestEvent Deleted
                event(new EventDeleted($eventServiceRequest));
            } //End if
            
            //Assign to the return value
            $objReturnValue = $eventServiceRequest;

        } catch(AccessDeniedHttpException $e) {
            log::error('EventService:delete:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('EventService:delete:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('EventService:delete:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends