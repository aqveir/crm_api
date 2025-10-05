<?php

namespace Modules\ServiceRequest\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\ServiceRequest\Repositories\ServiceRequestRepository;
use Modules\ServiceRequest\Repositories\CommunicationRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\User\Repositories\User\UserRepository;

use Modules\Core\Services\BaseService;

use Modules\ServiceRequest\Events\Communication\MailCommunicationCreated;
use Modules\ServiceRequest\Events\Communication\SMSCommunicationCreated;
//use Modules\ServiceRequest\Events\Communication\EventDeleted;

use Modules\ServiceRequest\Notifications\SendMailToContactNotification;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

use Exception;
use Modules\Core\Exceptions\DuplicateDataException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class CommunicationService
 * @package Modules\ServiceRequest\Services
 */
class CommunicationService extends BaseService
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
     * @var \Modules\ServiceRequest\Repositories\CommunicationRepository
     */
    protected $communicationRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\ServiceRequest\Repositories\ServiceRequestRepository     $servicerequestRepository
     * @param \Modules\ServiceRequest\Repositories\CommunicationRepository      $communicationRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupRepository
     * @param \Modules\User\Repositories\User\UserRepository                    $userRepository
     * 
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        ServiceRequestRepository        $servicerequestRepository,
        CommunicationRepository         $communicationRepository,
        LookupValueRepository           $lookupRepository,
        UserRepository                  $userRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->servicerequestRepository = $servicerequestRepository;
        $this->communicationRepository  = $communicationRepository;
        $this->lookupRepository         = $lookupRepository;
        $this->userRepository           = $userRepository;
        
    } //Function ends


    /**
     * Send SMS to Contact
     * 
     * @param \string $orgHash
     * @param \string $srHash
     * @param \Illuminate\Support\Collection $payload
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function sendSMS(string $orgHash, string $srHash, Collection $payload, string $ipAddress=null)
    {
        $objReturnValue=null; $data=[];
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if (empty($organization)) { throw new BadRequestHttpException(); } //End if

            //Get ServiceRequest details by identifier
            $serviceRequest = $this->servicerequestRepository->getFullDataByIdentifier($organization['id'], $srHash);
            if (empty($serviceRequest)) { throw new BadRequestHttpException(); } //End if

            //Build data
            $data = $payload->only(['sms_message'])->toArray();
            $data = array_merge($data, [
                'org_id' => $organization['id'],
                'servicerequest_id' => $serviceRequest['id'] ,
                'start_at' => Carbon::now()
            ]);

            //Lookup Communication Type data - Mail
            $lookupCommType = $this->lookupRepository->getLookUpByKey($organization['id'], 'comm_type_sms');
            if (empty($lookupCommType)) { throw new BadRequestHttpException(); } //End if
            $data['activity_subtype_id'] = $lookupCommType['id'];

            //Lookup Communication Direction data - Outward
            $lookupCommDirection = $this->lookupRepository->getLookUpByKey($organization['id'], 'communication_direction_outgoing');
            if (empty($lookupCommDirection)) { throw new BadRequestHttpException(); } //End if
            $data['direction_id'] = $lookupCommDirection['id'];

            //Lookup Communication From Person data
            $lookupFromPersonType = $this->lookupRepository->getLookUpByKey($organization['id'], 'communication_person_type_user');
            if (empty($lookupFromPersonType)) { throw new BadRequestHttpException(); } //End if
            $data['from_person_type_id'] = $lookupFromPersonType['id'];
            $data['from_person_identifier_id'] = $user['id'];
            $data['sms_from'] = $user['phone'];

            //Lookup Communication To Person data
            $lookupToPersonType = $this->lookupRepository->getLookUpByKey($organization['id'], 'communication_person_type_contact');
            if (empty($lookupToPersonType)) { throw new BadRequestHttpException(); } //End if
            $data['to_person_type_id'] = $lookupToPersonType['id'];
            $data['to_person_identifier_id'] = $serviceRequest->contact['id'];
            $data['sms_to'] = $serviceRequest->contact['full_name'];

            //Create Communication - Mail
            $comm = $this->communicationRepository->create($data, $user['id'], $ipAddress);
            $comm->makeVisible(['sms_message']);

            //Send Mail
            //Notification::send($serviceRequest->contact, new SendMailToContactNotification($comm, $user));
                
            //Raise event: Mail Communication Created
            event(new SMSCommunicationCreated($comm));                

            //Assign to the return value
            $objReturnValue = $comm;

        } catch(AccessDeniedHttpException $e) {
            log::error('CommunicationService:sendMail:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('CommunicationService:sendMail:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('CommunicationService:sendMail:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Send Mail to Contact
     * 
     * @param \string $orgHash
     * @param \string $srHash
     * @param \Illuminate\Support\Collection $payload
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function createCall(
        string $orgHash, string $srHash, Collection $payload, 
        string $directionKey='communication_direction_outgoing', 
        string $ipAddress=null)
    {
        $objReturnValue=null; $data=[];
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if (empty($organization)) { throw new BadRequestHttpException(); } //End if

            //Get ServiceRequest details by identifier
            $serviceRequest = $this->servicerequestRepository->getFullDataByIdentifier($organization['id'], $srHash);
            if (empty($serviceRequest)) { throw new BadRequestHttpException(); } //End if

            //Build data
            $data = $payload->toArray();
            $data = array_merge($data, [
                'org_id' => $organization['id'],
                'servicerequest_id' => $serviceRequest['id'] ,
                'start_at' => Carbon::now()
            ]);

            //Lookup Communication Type data - Mail
            $lookupCommType = $this->lookupRepository->getLookUpByKey($organization['id'], 'comm_type_phone');
            if (empty($lookupCommType)) { throw new BadRequestHttpException(); } //End if
            $data['activity_subtype_id'] = $lookupCommType['id'];

            //Lookup Communication Direction data - Outward
            $lookupCommDirection = $this->lookupRepository->getLookUpByKey($organization['id'], 'communication_direction_outgoing');
            if (empty($lookupCommDirection)) { throw new BadRequestHttpException(); } //End if
            $data['direction_id'] = $lookupCommDirection['id'];

            //Lookup Communication From Person data
            $lookupFromPersonType = $this->lookupRepository->getLookUpByKey($organization['id'], 'communication_person_type_user');
            if (empty($lookupFromPersonType)) { throw new BadRequestHttpException(); } //End if
            $data['from_person_type_id'] = $lookupFromPersonType['id'];
            $data['from_person_identifier_id'] = $user['id'];
            $data['call_from'] = $user['full_name'];

            //Lookup Communication To Person data
            $lookupToPersonType = $this->lookupRepository->getLookUpByKey($organization['id'], 'communication_person_type_contact');
            if (empty($lookupToPersonType)) { throw new BadRequestHttpException(); } //End if
            $data['to_person_type_id'] = $lookupToPersonType['id'];
            $data['to_person_identifier_id'] = $serviceRequest->contact['id'];
            $data['call_to'] = $serviceRequest->contact['full_name'];

            //Create Communication - Mail
            $comm = $this->communicationRepository->create($data, $user['id'], $ipAddress);
            //$comm->makeVisible(['email_subject', 'email_body']);
                
            //Raise event: Voice Call Communication Created
            //event(new MailCommunicationCreated($comm));                

            //Assign to the return value
            $objReturnValue = $comm;

        } catch(AccessDeniedHttpException $e) {
            log::error('CommunicationService:sendMail:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('CommunicationService:sendMail:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('CommunicationService:sendMail:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Send Mail to Contact
     * 
     * @param \string $orgHash
     * @param \string $srHash
     * @param \Illuminate\Support\Collection $payload
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function sendMail(string $orgHash, string $srHash, Collection $payload, string $ipAddress=null)
    {
        $objReturnValue=null; $data=[];
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if (empty($organization)) { throw new BadRequestHttpException(); } //End if

            //Get ServiceRequest details by identifier
            $serviceRequest = $this->servicerequestRepository->getFullDataByIdentifier($organization['id'], $srHash);
            if (empty($serviceRequest)) { throw new BadRequestHttpException(); } //End if

            //Build data
            $data = $payload->only(['email_subject', 'email_body'])->toArray();
            $data = array_merge($data, [
                'org_id' => $organization['id'],
                'servicerequest_id' => $serviceRequest['id'] ,
                'start_at' => Carbon::now()
            ]);

            //Lookup Communication Type data - Mail
            $lookupCommType = $this->lookupRepository->getLookUpByKey($organization['id'], 'comm_type_email');
            if (empty($lookupCommType)) { throw new BadRequestHttpException(); } //End if
            $data['activity_subtype_id'] = $lookupCommType['id'];

            //Lookup Communication Direction data - Outward
            $lookupCommDirection = $this->lookupRepository->getLookUpByKey($organization['id'], 'communication_direction_outgoing');
            if (empty($lookupCommDirection)) { throw new BadRequestHttpException(); } //End if
            $data['direction_id'] = $lookupCommDirection['id'];

            //Lookup Communication From Person data
            $lookupFromPersonType = $this->lookupRepository->getLookUpByKey($organization['id'], 'communication_person_type_user');
            if (empty($lookupFromPersonType)) { throw new BadRequestHttpException(); } //End if
            $data['from_person_type_id'] = $lookupFromPersonType['id'];
            $data['from_person_identifier_id'] = $user['id'];
            $data['email_from'] = $user['full_name'];

            //Lookup Communication To Person data
            $lookupToPersonType = $this->lookupRepository->getLookUpByKey($organization['id'], 'communication_person_type_contact');
            if (empty($lookupToPersonType)) { throw new BadRequestHttpException(); } //End if
            $data['to_person_type_id'] = $lookupToPersonType['id'];
            $data['to_person_identifier_id'] = $serviceRequest->contact['id'];
            $data['email_to'] = $serviceRequest->contact['full_name'];

            //Create Communication - Mail
            $comm = $this->communicationRepository->create($data, $user['id'], $ipAddress);
            $comm->makeVisible(['email_subject', 'email_body']);

            //Send Mail
            Notification::send($serviceRequest->contact, new SendMailToContactNotification($comm, $user));
                
            //Raise event: Mail Communication Created
            event(new MailCommunicationCreated($comm));                

            //Assign to the return value
            $objReturnValue = $comm;

        } catch(AccessDeniedHttpException $e) {
            log::error('CommunicationService:sendMail:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('CommunicationService:sendMail:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('CommunicationService:sendMail:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Receive Mail from Contact
     * 
     * @param \string $orgHash
     * @param \string $srHash
     * @param \Illuminate\Support\Collection $payload
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function receiveMail(string $orgHash, string $srHash, Collection $payload, string $ipAddress=null)
    {
        $objReturnValue=null; $data=[];
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if (empty($organization)) { throw new BadRequestHttpException(); } //End if

            //Get ServiceRequest details by identifier
            $serviceRequest = $this->servicerequestRepository->getFullDataByIdentifier($organization['id'], $srHash);
            if (empty($serviceRequest)) { throw new BadRequestHttpException(); } //End if

            //Build data
            $data = $payload->only(['email_subject', 'email_body'])->toArray();
            $data = array_merge($data, [
                'org_id' => $organization['id'],
                'servicerequest_id' => $serviceRequest['id'] ,
                'start_at' => Carbon::now()
            ]);

            //Lookup Communication Type data - Mail
            $lookupCommType = $this->lookupRepository->getLookUpByKey($organization['id'], 'comm_type_email');
            if (empty($lookupCommType)) { throw new BadRequestHttpException(); } //End if
            $data['activity_subtype_id'] = $lookupCommType['id'];

            //Lookup Communication Direction data - Outward
            $lookupCommDirection = $this->lookupRepository->getLookUpByKey($organization['id'], 'communication_direction_outgoing');
            if (empty($lookupCommDirection)) { throw new BadRequestHttpException(); } //End if
            $data['direction_id'] = $lookupCommDirection['id'];

            //Lookup Communication From Person data
            $lookupFromPersonType = $this->lookupRepository->getLookUpByKey($organization['id'], 'communication_person_type_user');
            if (empty($lookupFromPersonType)) { throw new BadRequestHttpException(); } //End if
            $data['from_person_type_id'] = $lookupFromPersonType['id'];
            $data['from_person_identifier_id'] = $user['id'];

            //Lookup Communication To Person data
            $lookupToPersonType = $this->lookupRepository->getLookUpByKey($organization['id'], 'communication_person_type_contact');
            if (empty($lookupToPersonType)) { throw new BadRequestHttpException(); } //End if
            $data['to_person_type_id'] = $lookupToPersonType['id'];
            $data['to_person_identifier_id'] = $serviceRequest->contact['id'];

            //Create Communication - Mail
            $comm = $this->communicationRepository->create($data, $user['id'], $ipAddress);
            $comm->makeVisible(['email_subject', 'email_body']);

            //Send Mail
            Notification::send($serviceRequest->contact, new SendMailToContactNotification($comm, $user));
                
            //Raise event: Mail Communication Created
            event(new MailCommunicationCreated($comm));                

            //Assign to the return value
            $objReturnValue = $comm;

        } catch(AccessDeniedHttpException $e) {
            log::error('CommunicationService:sendMail:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('CommunicationService:sendMail:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('CommunicationService:sendMail:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update ServiceRequestEvent
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \int $commId
     *
     * @return mixed
     */
    public function update(string $orgHash, string $srHash, Collection $payload, int $commId, string $ipAddress=null)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if (empty($organization)) { throw new BadRequestHttpException(); } //End if

            //Get ServiceRequest details by identifier
            $serviceRequest = $this->servicerequestRepository->getFullDataByIdentifier($organization['id'], $srHash);
            if (empty($serviceRequest)) { throw new BadRequestHttpException(); } //End if

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
            $task = $this->communicationRepository->update($eventId, 'id', $data, $user['id']);
                
            //Raise event: ServiceRequestEvent Updated
            event(new TaskUpdatedEvent($task));                

            //Assign to the return value
            $objReturnValue = $task;

        } catch(AccessDeniedHttpException $e) {
            log::error('CommunicationService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('CommunicationService:update:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('CommunicationService:update:Exception:' . $e->getMessage());
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
            $eventServiceRequest = $this->communicationRepository->delete($eventId, 'id', $user['id'], $ipAddress);
            if ($eventServiceRequest) {
                //Raise event: ServiceRequestEvent Deleted
                event(new EventDeleted($eventServiceRequest));
            } //End if
            
            //Assign to the return value
            $objReturnValue = $eventServiceRequest;

        } catch(AccessDeniedHttpException $e) {
            log::error('CommunicationService:delete:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('CommunicationService:delete:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('CommunicationService:delete:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends