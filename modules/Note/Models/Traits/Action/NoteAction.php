<?php

namespace Modules\Note\Models\Traits\Action;

use Modules\Note\Models\Note;

use Config;
use Illuminate\Support\Facades\Log;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class Method on Note
 */
trait NoteAction
{

	public function createNote($request)
	{
		$objReturnValue=null;
		try {  
			//Get request parameters and save
			$note = new Note($request);
			$note->created_by=$request['user_id'];
			$note->modified_by=$request['user_id'];

			if(!$note->save()) {
				throw new HttpException(500);
			} //End if

			//Initiate action as per Note Source
			$noteType = $this->getLookUpById($request['org_id'], $request['entity_type']);
			if($noteType) {
				$orgId = $request['org_id'];
				switch ($noteType['value']) {
					//Service Request
					case config('portiqo-crm.settings.lookup_value.service_request'):
						//Get SR data
						$serviceRequest = $this->getServiceRequestById($orgId, $request['reference_id']);

						//Update SR Records
						$isUpdatedStatus = $this->updateServiceRequest($orgId, $serviceRequest['hash'], null, $request['user_id'], true);
						break;
					
					default:
						# code...
						break;
				}				
			} //End if

	        $objReturnValue = $note;		
		} catch(Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-catch ends
		
		return $objReturnValue;
	} //End Function

} //Trait ends
