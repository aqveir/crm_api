<?php

namespace App\Models\Document\Traits\Action;

use Config;

use Carbon\Carbon;
use DB;

use App\Jobs\updateElasticSearchJob;
use App\Models\Document\Document;

use App\Events\ServiceRequest\ServiceRequestPropertyFilterEvent;
use App\Events\ServiceRequest\ServiceRequestUpdateEvent;
use App\Events\ServiceRequest\ServiceRequestCreateEvent;
use App\Events\ServiceRequest\UpdateElasticSearchEvent;

use App\Models\ServiceRequest\ServiceRequest;
use App\Models\Lookup\Traits\Action\LookupValueAction;
use App\Models\ServiceRequest\ServiceRequestEvent;
use App\Models\ServiceRequest\ServiceRequestRecommendations;

use Illuminate\Support\Facades\Log;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use App\Exceptions\DuplicateException;

/**
 * Class Method on Document Uploads
 */
trait DocumentAction
{
    /**
     * Save Uploaded Documents
     * @return objReturnValue
     */
    public function saveUploadedDcouments(string $document_url, $request)
    {
        $objReturnValue=null;
        try {
            $query = new Document($request);
            $query->is_active  = 1;
            $query->file_path  = $document_url;
            $query->created_by = $request['user_id'];
            $query->modified_by= $request['user_id'];

            if(!$query->save()) {
                throw new HttpException(500);
            } //End if

            //Initiate action as per Document Source
            $documentType = $this->getLookUpById($request['org_id'], $request['entity_type']);
            if($documentType) {
                $orgId = $request['org_id'];
                switch ($documentType['value']) {
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

            $objReturnValue = $query;
        } catch (Exception $e) {
            $objReturnValue=null;
        } //Try-Catch ends

        return $objReturnValue;
    }  //End Function
    
    /**
     * Update Document Active Or Inactive
     * @return objReturnValue
     */
    public function updateDocumentUploaded(int $orgId, int $documentId, int $userId, bool $isInactive=false)
    {
        $objReturnValue=false;
        try {
            $query = config('portiqo-crm.class_model.document')::where('id', $documentId);
            if($isInactive) {
                $query = $query->update([
                        'is_active'=> 0, 
                        'modified_on'=> Carbon::now()
                    ]);   
            } else {
                $query = $query->update([
                        'is_active'=> 1, 
                        'modified_on'=> Carbon::now()
                    ]);   
            }//End if-else

            if($query) {
                $document = $this->getDocumentById($documentId);
                if(!$document) { throw new NotFoundHttpException(); } //End if

                $serviceRequest = $this->getServiceRequestById($orgId, $document['reference_id']);
                //log::debug('ServiceRequest->' .json_encode($serviceRequest, JSON_PRETTY_PRINT));

                //Update SR Records
                $isUpdatedStatus = $this->updateServiceRequest($orgId, $serviceRequest['hash'], null, $userId, true);
                //log::debug('ServiceRequest isUpdatedStatus->' .json_encode($isUpdatedStatus, JSON_PRETTY_PRINT));
            } //End if

            $objReturnValue=true;
        } catch (Exception $e) {
            $objReturnValue=false;
        } //Try-Catch ends

        return $objReturnValue;
    }  //End Function


    /**
     * Get Document Object By Id 
     * @return objReturnValue
     */
    private function getDocumentById(int $documentId)
    {
        $objReturnValue=null;
        try {
            $query = config('portiqo-crm.class_model.document')::where('id', $documentId);
            $query = $query->firstOrFail();

            $objReturnValue=$query;
        } catch (Exception $e) {
            log::error(json_encode($e));
            $objReturnValue=null;
        } //Try-Catch ends

        return $objReturnValue;
    }  //End Function
} //Trait ends
