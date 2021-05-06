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
     * Create Task
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function create(string $orgHash, string $srHash, Collection $payload, bool $isAutoCreated=false, string $ipAddress=null)
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
            $serviceRequest = $this->servicerequestRepository->getServiceRequestByHash($organization['id'], $srHash);
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

            //Lookup Task Priority data
            $lookupPriority = $this->lookupRepository->getLookUpByKey($organization['id'], $payload['priority_key']);
            if (empty($lookupPriority)) { throw new BadRequestHttpException(); } //End if
            $data['priority_id'] = $lookupPriority['id'];

            //Create Task
            $task = $this->taskRepository->create($data);
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
            $data = $payload->only(['subject', 'description', 'scheduled_at', 'completed_at'])->toArray();

            //Get Assignee User by Hash
            $assigneeUser = $this->userRepository->getDataByHash($organization['id'], $payload['assignee_uHash']);
            if (empty($assigneeUser)) { throw new BadRequestHttpException(); } //End if
            $data['user_id'] = $assigneeUser['id'];

            //Lookup SubType data
            $lookupSubType = $this->lookupRepository->getLookUpByKey($organization['id'], $payload['subtype_key']);
            if (empty($lookupSubType)) { throw new BadRequestHttpException(); } //End if
            $data['subtype_id'] = $lookupSubType['id'];

            //Lookup Task Priority data
            $lookupPriority = $this->lookupRepository->getLookUpByKey($organization['id'], $payload['priority_key']);
            if (empty($lookupPriority)) { throw new BadRequestHttpException(); } //End if
            $data['priority_id'] = $lookupPriority['id'];

            //Update IP address
            $data['ip_address'] = $ipAddress;

            //Update Task
            $task = $this->taskRepository->update($taskId, 'id', $data, $user['id']);
                
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