<?php

namespace Modules\Contact\Repositories\Contact;

use Modules\Contact\Contracts\{ContactDetailContract};

use Modules\Contact\Models\Contact\ContactDetail;
use Modules\Core\Repositories\EloquentRepository;

use Illuminate\Support\Facades\Log;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class ContactDetailRepository
 * 
 * @package App\Repositories\Contact
 */
class ContactDetailRepository extends EloquentRepository
{

    /**
     * ContactDetailRepository constructor.
     *
     * @param  ContactDetail  $model
     */
    public function __construct(ContactDetail $model)
    {
        $this->model = $model;
    }


    /**
     * Get Contact Detail By ContactId, TypeId
     */
    public function getContactDetailsByTypeId(int $orgId, int $contactId, int $typeId, bool $isPrimary=null, strng $proxy=null)
    {
        $objReturnValue=null;
        try {
            $query = $this->model;
            $query = $query->where('org_id', $orgId);
            $query = $query->where('contact_id', $contactId);
            $query = $query->where('type_id', $typeId);
            if(!empty($proxy)) { $query = $query->where('proxy', $proxy); } //End if 
            if(!empty($isPrimary)) { $query = $query->where('is_primary', $isPrimary); } //End if
            $query = $query->firstOrFail();

            //Get the Contact Object
            $objReturnValue = $query;
        } catch(ModelNotFoundException $e) {
            $objReturnValue=null;
            Log::error('ContactDetailRepository:getContactDetailsByTypeId:ModelNotFoundException:' . $e->getMessage());
            throw new ModelNotFoundException();
        } catch (Exception $e) {
            $objReturnValue=null;
            Log::error('ContactDetailRepository:getContactDetailsByTypeId:Exception:' . $e->getMessage());
        } ///Try-Catch ends
        return $objReturnValue;
    } //Function ends


    /**
     * Get Contact Detail By Type and Identifier Details
     */
    public function getContactDetailByIdentifier(int $orgId, string $identifier, int $typeId=null, bool $isPrimary=null, bool $isActive=null)
    {
        $objReturnValue=null;
        try {
            $query = $this->model;
            $query = $query->where('org_id', $orgId);
            $query = $query->where('identifier', $identifier);        
            if(!empty($typeId)) { $query = $query->where('type_id', $typeId); } //End if
            if(!empty($isPrimary)) { $query = $query->where('is_primary', $isPrimary); } //End if
            if(!empty($isActive)) { $query = $query->where('is_active', $isActive); } //End if
            $query = $query->firstOrFail();

            //Get the Contact Object
            $objReturnValue = $query;
        } catch(ModelNotFoundException $e) {
            $objReturnValue=null;
            Log::debug('ContactDetailRepository:getContactDetailByIdentifier:ModelNotFoundException:' . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            $objReturnValue=null;
            Log::debug('ContactDetailRepository:getContactDetailByIdentifier:Exception:' . $e->getMessage());
        } //Try-Catch ends
        return $objReturnValue;
    } //Function ends


    /**
     * Get Contact Detail by the key provided array
     * 
     * @param int $orgId
     * @param  array  $arrDetails
     *
     * @return boolean
     */
    public function getContactDetailByIdentifiers(int $orgId, array $arrDetails)
    {
        $objReturnValue = null;
        try {
            if (!empty($arrDetails) && is_array($arrDetails) && count($arrDetails)>0)
            {
                //Loop the array
                foreach ($arrDetails as $detail) {
                    //Check the content in the array
                    if (in_array($detail['type_key'], [
                            config('aqveir.settings.static.key.lookup_value.email'), 
                            config('aqveir.settings.static.key.lookup_value.phone')
                        ], false)) 
                    {
                        try {
                            $data = $this->getContactDetailByIdentifier($orgId, $detail['identifier']);

                            //Record exists
                            $objReturnValue = ((!empty($data))?$data:$objReturnValue);
                        } catch(ModelNotFoundException $e) {
                            $objReturnValue=$objReturnValue;
                            Log::debug('ContactDetailRepository:getContactDetailByIdentifiers:ModelNotFoundException:' . $e->getMessage());
                        } //Try-Catch ends
                    } //End if
                } //Loop ends         
            } //End if
        }   catch(Exception $e) {
            throw $e;
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Calidate if the contact detail exists
     * 
     * @param int $orgId
     * @param  array  $contactDetails
     * 
     * @return boolean
     */
    public function exits(int $orgId, array $contactDetails): bool
    {
        return !empty($this->getContactDetailByIdentifiers($orgId, $contactDetails));
    } //Function ends


    /**
     * Get Contact By Proxy
     */
    public function getContactByProxy(int $orgId, string $proxy)
    {
       $objReturnValue = null;
       try {
            $query = $this->model;
            $query = $query->where('org_id', $orgId);
            $query = $query->where('proxy', '=', $proxy);
            $query = $query->firstOrFail();

            $objReturnValue = $query;
        } catch (Exception $e) {
            $objReturnValue=null;
            Log::error(json_encode($e));
        } //Try-Catch ends
       return $objReturnValue;
    } //Function ends

    
    /**
    * Get massked Identifier By Type
    */  
    // public function getMaskedData(int $orgId, int $customerId, $dataType='', bool $isUnMasskedIdentifier=false) 
    // {
    //     $objReturnValue=null;
    //     try {
    //         switch ($dataType) {
    //             case 'primary_email':
    //                 $type = $this->getLookUpByValue($orgId, config('portiqo-crm.settings.lookup_value.email'));
    //                 $data = $this->getContactDetailsByType($customerId, $type['id'], null);
    //                 $dataMasked = ($isUnMasskedIdentifier)?$data:$this->getMaskedDataByType($type['value'], $data);
    //                 break;
    //             case 'primary_phone':
    //                 $type = $this->getLookUpByValue($orgId, config('portiqo-crm.settings.lookup_value.phone'));
    //                 $data = $this->getContactDetailsByType($customerId, $type['id'], null);        
    //                 $dataMasked = ($isUnMasskedIdentifier)?$data:$this->getMaskedDataByType($type['value'], $data);
    //                 break;
                
    //             default:
    //                 $dataMasked=null;
    //                 break;
    //         } //Switch ends

    //         $objReturnValue=$dataMasked;
    //     } catch(Exception $e) {
    //         $objReturnValue=null;
    //         Log::error($e);
    //     } //Try-catch ends

    //     return $objReturnValue;
    // } //Function ends

} //Class ends
