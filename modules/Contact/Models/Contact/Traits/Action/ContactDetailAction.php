<?php

namespace Modules\Contact\Models\Contact\Traits\Action;

use Config;
use Illuminate\Support\Facades\Log;
use Exception;

use Modules\Contact\Models\Contact\ContactDetail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Action methods on Contact Detail
 */
trait ContactDetailAction
{
    /**
     * Get Contact Detail By ContactId, Type and Default Status
     */
    public function getContactDetailsByType($customerId, $typeId, $isPrimary=null, $proxy='',  $id=0)
    {
        $objReturnValue=null;
        try {
            $query = ContactDetail::with(['country']);
            $query = $query->where('Contact_id', $customerId);
            $query = $query->where('type', $typeId);
            if($proxy != '') { $query = $query->where('proxy', $proxy); } //End if 
            if($isPrimary) { $query = $query->where('is_primary', 1); } //End if
            if($id>0) { $query = $query->where('id', $id); } //End if
            $query = $query->firstOrFail();
            
            //Get the Contact Object
            $objReturnValue = $query;
        } catch (Exception $e) {
            $objReturnValue=null;
            Log::error(json_encode($e));
        } ///Try-Catch ends
        return $objReturnValue;
    } //Function ends

    /**
     * Get Contact Detail By Type and Identifier Details
     */
    public function getContactDetailByIdentifier($identifier, int $typeId=null, $isPrimary=null)
    {
        $objReturnValue=null;
        try {
            $query = ContactDetail::with(['country']);
            $query = $query->where('identifier', 'like', '%'.$identifier);
            if($typeId) { $query = $query->where('type', $typeId); } //End if
            if($isPrimary) { $query = $query->where('is_primary', 1); } //End if
            $query = $query->firstOrFail();

            //Get the Contact Object
            $objReturnValue = $query;
        } catch (Exception $e) {
            $objReturnValue=null;
            Log::error(json_encode($e));
        } //Try-Catch ends
        return $objReturnValue;
    } //Function ends


    /**
     * Get Contact By Proxy
     */
    public function getContactByProxy(int $orgId, string $proxy)
    {
       $objReturnValue = null;
       try {
            $query = ContactDetail::where('org_id', $orgId);
            $query = $query->where('proxy', '=', $proxy)->firstOrFail();

            $objReturnValue = $query;
        } catch (Exception $e) {
            $objReturnValue=null;
            Log::error(json_encode($e));
        } //Try-Catch ends
       return $objReturnValue;
    }//Function ends


    /**
     * Function to return the Contact number of a Contact
     *
     * @return objReturnValue
     */
    public function getContactPhone(int $orgId=0, $contact, string $proxy=null, $isPrimary=null)
    {
        $objReturnValue = null;
        try {
            //Get Phone Type for the Organization
            $type = $this->getLookUpByValue($orgId, config('portiqo-crm.settings.lookup_value.phone'));

            //Get phone details for a Contact
            $isPrimary = ($proxy!=null)?null:true;
            $customerDetail = $this->getContactDetailsByType($contact['id'], $type->id, $isPrimary, $proxy);

            if($customerDetail!=null) {
                $country_code = $customerDetail['country']['code'];
                $phone_number = $customerDetail['identifier'];
                $objReturnValue = '+'.$country_code.$phone_number;
            } else { throw new BadRequestHttpException(); } //End if-else
            
        } catch(Exception $e) {
            Log::error(json_encode($e));
            throw new NotFoundHttpException();
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends


    /**
    * Create New ContactDetail By Contact
    *
    * @return objReturnValue
    */
    public function createNewContactDetails($customerId, $identifier, int $typeId, int $orgId)
    {
        $objReturnValue = null;
        try {
            $customerDetail = ContactDetail::create([
                'contact_id' => $customerId,
                'identifier' => $identifier,
                'type'       => $typeId,
                'is_primary'  => 1
            ]);
            $customerDetail->org_id=$orgId;

            if(!$customerDetail->save()) {
               throw new HttpException(500);
            } //End if

            $objReturnValue = $customerDetail;
        } catch(Exception $e) {
            Log::error(json_encode($e));
            $objReturnValue = null;
            throw new NotFoundHttpException();
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends


    /**
    * Get massked Identifier By Type
    */  
    public function getMaskedData(int $orgId, int $customerId, $dataType='', bool $isUnMasskedIdentifier=false) 
    {
        $objReturnValue=null;
        try {
            switch ($dataType) {
                case 'primary_email':
                    $type = $this->getLookUpByValue($orgId, config('portiqo-crm.settings.lookup_value.email'));
                    $data = $this->getContactDetailsByType($customerId, $type['id'], null);
                    $dataMasked = ($isUnMasskedIdentifier)?$data:$this->getMaskedDataByType($type['value'], $data);
                    break;
                case 'primary_phone':
                    $type = $this->getLookUpByValue($orgId, config('portiqo-crm.settings.lookup_value.phone'));
                    $data = $this->getContactDetailsByType($customerId, $type['id'], null);        
                    $dataMasked = ($isUnMasskedIdentifier)?$data:$this->getMaskedDataByType($type['value'], $data);
                    break;
                
                default:
                    $dataMasked=null;
                    break;
            } //Switch ends

            $objReturnValue=$dataMasked;
        } catch(Exception $e) {
            $objReturnValue=null;
            Log::error($e);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends



    /**
    * Get massked Identifier By Type and Identifier
    */
    public function getMaskedDataByType($dataType, $data)
    {
        $objReturnValue=null;
        try {
            $posStart = 0;
            $lengthOfTrailUnchanged = 0;

            switch ($dataType) {
                case config('portiqo-crm.settings.lookup_value.email'):
                    $posStart = 2;
                    $lengthOfTrailUnchanged = 4;

                    $masskedData = substr_replace($data['identifier'], str_repeat('X', (strlen($data['identifier'])-($lengthOfTrailUnchanged+$posStart))), $posStart, (strlen($data['identifier'])-($lengthOfTrailUnchanged+$posStart)));
                    $dataMasked = ($masskedData)?$masskedData:null;
                    break;

                case config('portiqo-crm.settings.lookup_value.phone'):
                    $posStart = 1;
                    $lengthOfTrailUnchanged = 3;
                    
                    $masskedData = substr_replace($data['identifier'], str_repeat('X', (strlen($data['identifier'])-($lengthOfTrailUnchanged+$posStart))), $posStart, (strlen($data['identifier'])-($lengthOfTrailUnchanged+$posStart)));
                    $dataMasked = ($masskedData)?$masskedData:null;
                    break; 

                default:
                    $dataMasked=null;
                    break;       
            } //Switch ends

            //Assign Massked Identifier
            $data['identifier'] = $dataMasked;

            $objReturnValue = $data;
        } catch (Exception $e) {
            $objReturnValue=null;
            Log::error($e);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Trait ends
