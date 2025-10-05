<?php

namespace Modules\ServiceRequest\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\ServiceRequest\Repositories\ServiceRequestRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\User\Repositories\User\UserRepository;
use Modules\ServiceRequest\Repositories\TaskRepository;

use Modules\Core\Services\BaseService;

use Modules\ServiceRequest\Traits\ParticipantTrait;

use Modules\ServiceRequest\Events\Task\TaskCreatedEvent;
use Modules\ServiceRequest\Events\Task\TaskUpdatedEvent;
use Modules\ServiceRequest\Events\Task\TaskDeletedEvent;

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
 * Class TaskService
 * @package Modules\ServiceRequest\Services
 */
class TaskService extends BaseService
{
    use ParticipantTrait;

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
     * @var \Modules\ServiceRequest\Repositories\TaskRepository
     */
    protected $taskRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\ServiceRequest\Repositories\ServiceRequestRepository     $servicerequestRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupRepository
     * @param \Modules\User\Repositories\User\UserRepository                    $userRepository
     * @param \Modules\ServiceRequest\Repositories\TaskRepository               $taskRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        ServiceRequestRepository        $servicerequestRepository,
        LookupValueRepository           $lookupRepository,
        UserRepository                  $userRepository,
        TaskRepository                  $taskRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->servicerequestRepository = $servicerequestRepository;
        $this->lookupRepository         = $lookupRepository;
        $this->userRepository           = $userRepository;
        $this->taskRepository           = $taskRepository;
    } //Function ends


    /**
     * Get All Task Data for an Oraganization (Backend)
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

            //Get Activity Type: Task Lookup data
            $typeTask = $this->lookupRepository->getLookUpByKey($organization['id'], 'service_request_activity_type_task');

            //Load Task Data
            $objReturnValue = $this->taskRepository
                ->getFullData($organization['id'], $typeTask['id'], $isForcedFromDB, $page, $size);

        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Get Task Data by Identifier (Backend)
     * 
     * @param  \string $orgHash
     * @param  \Illuminate\Support\Collection $payload
     * @param  \int $taskId
     * 
     * @return object
     */
    public function show(string $orgHash, Collection $payload, int $taskId)
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

            //Get Activity Type: Task Lookup data
            $typeTask = $this->lookupRepository->getLookUpByKey($organization['id'], 'service_request_activity_type_task');

            //Load Task Data
            $objReturnValue = $this->taskRepository
                ->getFullDataByIdentifier($organization['id'], $typeTask['id'], $taskId, $isForcedFromDB);

        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Create Task
     * 
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
            $data = $payload->only(['subject', 'description', 'start_at', 'end_at'])->toArray();

            $createdBy = 0;
            if (!$isAutoCreated) {
                //Authenticated User
                $user = $this->getCurrentUser('backend');

                $createdBy = $user['id'];
            } //End if

            //Set Organization data
            $data['org_id'] = $organization['id'];

            //Get ServiceRequest details by identifier
            $serviceRequest = $this->servicerequestRepository->getFullDataByIdentifier($organization['id'], $srHash);
            if (empty($serviceRequest)) { throw new BadRequestHttpException(); } //End if
            $data['servicerequest_id'] = $serviceRequest['id'];

            //Lookup Type data
            $data['type_id'] = $this->getLookupValueId($organization['id'], $payload, null, 'service_request_activity_type_task');

            //Lookup SubType data
            $data['subtype_id'] = $this->getLookupValueId($organization['id'], $payload, 'subtype_key', 'comm_type_other');

            //Lookup Task Priority data
            $data['priority_id'] = $this->getLookupValueId($organization['id'], $payload, 'priority_key', 'priority_normal');

            //Lookup Task Status data
            $data['status_id'] = $this->getLookupValueId($organization['id'], $payload, 'status_key', 'task_status_not_started');

            //Create assignee data
            $assigneeCollection = $this->getParticipants($organization, $payload['assignee']);

            //Create Task
            $task = $this->taskRepository->create($data, $createdBy, $ipAddress);
            if ($assigneeCollection && count($assigneeCollection)>0) {
                $task->assignee()->create($assigneeCollection[0]);
            } //End if
            
            $task->load('subtype', 'priority', 'assignee', 'owner');
                
            //Raise event: Task Created
            event(new TaskCreatedEvent($task, $isAutoCreated));                

            //Assign to the return value
            $objReturnValue = $task;

        } catch(AccessDeniedHttpException $e) {
            log::error('TaskService:create:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('TaskService:create:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('TaskService:create:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update Task
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \int $taskId
     *
     * @return mixed
     */
    public function update(string $orgHash, string $srHash, Collection $payload, int $taskId, string $ipAddress=null)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if (empty($organization)) { throw new BadRequestHttpException(); } //End if

            //Build data
            $data = $payload->only(['subject', 'description', 'start_at', 'end_at'])->toArray();

            //Lookup SubType data
            $data['subtype_id'] = $this->getLookupValueId($organization['id'], $payload, 'subtype_key', 'comm_type_other');

            //Lookup Task Priority data
            $data['priority_id'] = $this->getLookupValueId($organization['id'], $payload, 'priority_key', 'priority_normal');

            //Lookup Task Status data
            $data['status_id'] = $this->getLookupValueId($organization['id'], $payload, 'status_key', 'task_status_not_started');
            if ($payload['status_key']=='task_status_completed') {
                $payload['completed_at'] = Carbon.now();
            } //End if

            //Create assignee data
            $assigneeCollection = $this->getParticipants($organization, $payload['assignee']);

            //Update Task
            $task = $this->taskRepository->update($taskId, 'id', $data, $user['id'], $ipAddress);
            $task->assignee()->update($assigneeCollection[0]);
                
            //Raise event: Task Updated
            event(new TaskUpdatedEvent($task));                

            //Assign to the return value
            $objReturnValue = $task;

        } catch(AccessDeniedHttpException $e) {
            log::error('TaskService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('TaskService:update:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('TaskService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Delete Task
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \int $taskId
     *
     * @return mixed
     */
    public function delete(string $orgHash, string $srHash, Collection $payload, int $taskId, string $ipAddress=null)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Delete Task
            $response = $this->taskRepository->deleteById($noteId, $user['id']);
            if ($response) {
                //Raise event: Task Deleted
                event(new TaskDeletedEvent($note));
            } //End if
            
            //Assign to the return value
            $objReturnValue = $response;

        } catch(AccessDeniedHttpException $e) {
            log::error('TaskService:delete:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('TaskService:delete:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('TaskService:delete:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends