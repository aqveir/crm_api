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
    public function getContactDetailsByIdentifier(int $orgId, string $identifier, int $typeId=null, bool $isPrimary=null, bool $isActive=null)
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
            Log::error('ContactDetailRepository:getContactDetailsByIdentifier:ModelNotFoundException:' . $e->getMessage());
            throw new ModelNotFoundException();
        } catch (Exception $e) {
            $objReturnValue=null;
            Log::error('ContactDetailRepository:getContactDetailsByIdentifier:Exception:' . $e->getMessage());
        } //Try-Catch ends
        return $objReturnValue;
    } //Function ends


    /**
     * @param $item
     * @param $column
     * @param  array  $columns
     *
     * @return boolean
     */
    public function validate(int $orgId, array $arrData)
    {
        $objReturnValue = false;
        try {
            if (!empty($arrData))
            {
                $keys = array_keys($arrData);

                $maxRecords = count($keys);
                $recordCounter = 0;
                while (($maxRecords > $recordCounter) && !$objReturnValue) {
                    $key = $keys[$recordCounter];
                    $data = $this->getContactDetailsByIdentifier($orgId, $arrData[$key], null, true, true);

                    //Record exists
                    $objReturnValue = !empty($data);
                    
                    $recordCounter++;
                } //Loop ends         
            } //End if
        }   catch(ModelNotFoundException $e) {
            $objReturnValue=false;
            Log::error('ContactDetailRepository:validate:ModelNotFoundException:' . $e->getMessage());
        } catch(Exception $e) {
            throw new Exception($e);
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
    }//Function ends


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
