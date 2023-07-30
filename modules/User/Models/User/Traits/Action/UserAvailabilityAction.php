<?php

namespace Modules\User\Models\User\Traits\Action;

use Config;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Modules\Core\Models\Lookup\Traits\Action\LookupValueAction;

/**
 * Action methods on UserAvailability
 */
trait UserAvailabilityAction
{

	/**
	 * Set User Availability Status to Online
	 */
	public function setUserOnline(int $orgId, int $userId, int $modifiedBy=0, bool $isUserCurrentBusyOnly=false)
	{
		$objReturnValue=null;
		try {			
			//User Busy Check
			if($isUserCurrentBusyOnly) {
				$currentUserAvailability = $this->getUserAvailabilityStatus($orgId, $userId);
				//Log::debug($currentUserAvailability);

				$statusBusy = $this->getLookUpByValue($orgId, config('portiqo-crm.settings.lookup_value.user_availability_status_busy'));
				//Log::debug($statusBusy);
				
				if(!($currentUserAvailability && $statusBusy && ($currentUserAvailability['status_id']==$statusBusy->id))) { 
					throw new BadRequestHttpException(); 
				} //End if
			} //End if

			$status = $this->getLookUpByValue($orgId, config('portiqo-crm.settings.lookup_value.user_availability_status_online'));
			
            //Get the Contact Object
            $objReturnValue = $this->setUserAvailabilityStatus($orgId, $userId, $status->id, $modifiedBy);

		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-Catch ends
		return $objReturnValue;
	} //Function ends

	/**
	 * Set User Availability Status to Offline
	 */
	public function setUserOffline(int $orgId, int $userId, int $modifiedBy=0)
	{
		$objReturnValue=null;
		try {
			$status = $this->getLookUpByValue($orgId, config('portiqo-crm.settings.lookup_value.user_availability_status_offline'));

            //Get the Contact Object
            $objReturnValue = $this->setUserAvailabilityStatus($orgId, $userId, $status->id, $modifiedBy);
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-Catch ends
		return $objReturnValue;
	} //Function ends

	/**
	 * Set User Availability Status to Busy
	 */
	public function setUserBusy(int $orgId, int $userId, int $modifiedBy=0, bool $isUserCurrentOnlineOnly=false)
	{
		$objReturnValue=null;
		try {
			if($isUserCurrentOnlineOnly) {
				$currentUserAvailability = $this->getUserAvailabilityStatus($orgId, $userId);
				//Log::debug($currentUserAvailability);

				$statusOnline = $this->getLookUpByValue($orgId, config('portiqo-crm.settings.lookup_value.user_availability_status_online'));
				//Log::debug($statusOnline);
				
				if(!($currentUserAvailability && $statusOnline && ($currentUserAvailability['status_id']==$statusOnline->id))) { 
					throw new BadRequestHttpException(); 
				} //End if
			} //End if

			$status = $this->getLookUpByValue($orgId, config('portiqo-crm.settings.lookup_value.user_availability_status_busy'));

            //Get the Contact Object
            $objReturnValue = $this->setUserAvailabilityStatus($orgId, $userId, $status->id, $modifiedBy);
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-Catch ends
		return $objReturnValue;
	} //Function ends

	/**
	 * Set User Availability by Identifier
	 */
	public function setUserAvailabilityStatus(int $orgId, int $userId, int $statusId, int $modifiedBy=0)
	{
		$objReturnValue=null;
		try {
			$query = config('aqveir-class.class_model.user_availability')::updateOrCreate([
				'org_id' => $orgId,
				'user_id' => $userId
				], [
					'status_id' => $statusId, 
					'modified_by' => $modifiedBy,
					'modified_on' => \Carbon\Carbon::now()
				]);

            //Get the Contact Object
            $objReturnValue = $query;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-Catch ends
		return $objReturnValue;
	} //Function ends

	/**
	 * Get User Availability by Identifier
	 */
	public function getUserAvailabilityStatus(int $orgId, int $userId)
	{
		$objReturnValue=null;
		try {
			$query = config('aqveir-class.class_model.user_availability')::where([
				'org_id' => $orgId, 
				'user_id' => $userId
				])->firstOrFail();

            //Get the Contact Object
            $objReturnValue = $query;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-Catch ends
		return $objReturnValue;
	} //Function ends

	/**
	* Get OnlineUsers for Organization
	*/
	public function getOnlineUsers(int $orgId)
	{
		$objReturnValue=null;
		try {
			$query = config('aqveir-class.class_model.user')::with(['availability','country']);
			$query = $query->whereHas('availability.status', function ($inner_query) use ($orgId) {
				$inner_query->where('id', $this->getLookUpByValue($orgId, config('portiqo-crm.settings.lookup_value.user_availability_status_online'))->id);
			});
			$query = $query->where('org_id', $orgId);
			$query = $query->where('id','>', 0);
			$query = $query->inRandomOrder();
			$query = $query->get();

            //Get the Contact Object
            $objReturnValue = $query;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-Catch ends
		return $objReturnValue;
	} //Function ends


	/**
	* Get Status Active User
	*/
	public function getUserOnlineStatusById(int $orgId, int $userId)
	{
		$objReturnValue=null;
		try {
			$query = config('aqveir-class.class_model.user')::with(['availability']);
			$query = $query->where('id', $userId);
			$query = $query->where('org_id', $orgId);
			$query = $query->where('is_active', 1);
			$query = $query->whereHas('availability.status', function ($inner_query) use ($orgId) {
				$inner_query->where('id', $this->getLookUpByValue($orgId, config('portiqo-crm.settings.lookup_value.user_availability_status_online'))->id);
			});
			$query = $query->firstOrFail();

			$objReturnValue=$query;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-Catch ends
		return $objReturnValue;
	} //Function ends


	/**
	* Check User Last Login
	*/
	public function checkUserPastLogin(int $orgId, int $userId)
	{	
		$objReturnValue=null;
		try {
			$query = config('aqveir-class.class_model.user')::where('id', $userId);
			$query = $query->where('org_id', $orgId);
			$query = $query->where('is_active', 1);
			$query = $query->where('modified_on', '>=', Carbon::now()->subHours(config('portiqo-crm.default.user.availability.minimum_duration_for_lead_allocation')));
			
			$query = $query->firstOrFail();

			$objReturnValue=$query;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-catch ends

		return $objReturnValue;
	} //End Function


	/**
	* Allocate the leads to User based on availability
	*/
    public function allocateLeadToUsers(int $orgId, int $userId, int $userStatusId) {
        try {
            //Get online status
            $status = $this->getLookUpByValue($orgId, config('portiqo-crm.settings.lookup_value.user_availability_status_online'));
            //Log::debug($status);

            if($userStatusId==$status->id) {
                //Get New Service Requests for Owner
                $servicerequests = $this->getServiceRequestForOwner($orgId, $userId, 0, true);            
    
                //Get configuration params
                $minFreshLeadCount = (int) config('portiqo-crm.default.fresh_lead_count.min_fresh_lead_count');
                $setFreshLeadCount = (int) config('portiqo-crm.default.fresh_lead_count.set_fresh_lead_count');

                //Check the assignment condition
                if((($servicerequests) && (count($servicerequests)<=$minFreshLeadCount)) || 
                    ($servicerequests == null)) {
                    $servicerequestNotAssigned = $this->getNonAssignedServiceRequest($orgId);
                    //Log::debug($servicerequestNotAssigned);

                	$raw_leads=[]; $servicerequestNotAssignedSorted=[];
	                $task_data=[];
	                foreach ($servicerequestNotAssigned as &$servicerequest) {
	                	if($servicerequest->type!=null && $servicerequest->type!=[]) {
							$lead_priority = ucwords($servicerequest->type['value'][0]);

							if($servicerequest['tasks'] && count($servicerequest['tasks']->toArray())>0) {
								$hasTaskMissedCall = false;
								foreach ($servicerequest['tasks'] as $task) {
									$hasTaskMissedCall = $hasTaskMissedCall || ($task['subject']==config('portiqo-crm.default.service_request.task.missed_call.subject'));
			                	} //End Loop

			                	$task_subject = ($hasTaskMissedCall)?'M':'U';
							} else {
								$task_subject = 'U';
							} //End if
							$lead_priority .= $task_subject;

		                	$servicerequest['Lead_Prioritization'] = $lead_priority;
		                	array_push($raw_leads, $servicerequest);
	                	} //End if
	                } //End Loop

	                //Convert Array Of Data To Collection
	                $servicerequest_raw_leads = collect($raw_leads); 
	                $groupedLeadPriority = $servicerequest_raw_leads->groupBy('Lead_Prioritization')->toArray();

	                //Get Array Of Lead Prioritization From Config
	                $userDefinedLeadOrder = config('portiqo-crm.default.service_request.lead_prioritization');
	
	                foreach ($userDefinedLeadOrder as $leadkey) {
	                	if(array_key_exists($leadkey, $groupedLeadPriority)) {
                			$servicerequestNotAssignedSorted = array_merge($servicerequestNotAssignedSorted, $groupedLeadPriority[$leadkey]);	
	                	} //End if
	                } //End Foreach
	                
                    //Assign Leads
                    $leads = [];
                    $testLeadPriorityTypes=[];
                    if($servicerequestNotAssignedSorted && count($servicerequestNotAssignedSorted)>0) {
                        $assignmentCount=0;	
                        foreach($servicerequestNotAssignedSorted as $servicerequestNotAssignedLead) {		
            				if($assignmentCount<$setFreshLeadCount) {
            					array_push($leads, $servicerequestNotAssignedLead['id']);
            					array_push($testLeadPriorityTypes, $servicerequestNotAssignedLead['Lead_Prioritization']);
            				} else {
            					break;
            				} //End if-else
            		
            				$assignmentCount++;
                        } //End Foreach
                    } //End if
                    Log::debug('Lead Generated ->'.json_encode($leads));	             	

                    //Save the leads By OwnerId
                    $updatedServiceRequests = $this->updateServiceRequestsByOwnerId($orgId, $userId, $leads);
                    //Log::debug($updatedServiceRequests);     
                } //End if
            } //End if
        } catch(Exception $e) {
            Log::error(json_encode($e));
            throw new HttpException(500);
        } //Try-Catch ends
    } //Function ends
    
} //Trait ends