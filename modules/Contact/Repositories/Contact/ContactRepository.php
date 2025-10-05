<?php

namespace Modules\Contact\Repositories\Contact;

use Modules\Contact\Contracts\{ContactContract};

use Modules\Contact\Models\Contact\Contact;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class ContactRepository
 * 
 * @package Modules\Contact\Repositories\Contact
 */
class ContactRepository extends EloquentRepository implements ContactContract
{

    /**
     * Repository constructor.
     *
     * @param  Contact  $model
     */
    public function __construct(Contact $model)
    {
        $this->model = $model;
    }


    /**
     * Validate Contact Username
     * 
     * @param  int  $orgId
     * @param  string  $username
     *
     * @return boolean
     */
    public function validate(int $orgId, string $username)
    {
        $objReturnValue = false;
        try {
            $data = $this->model
                ->where('username', $username)
                ->where('org_id', $orgId)
                ->first();

            //Record exists
            $objReturnValue = !empty($data);            
        } catch(Exception $e) {
            throw new Exception($e);
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Get Full Customers Data for an Organization (Backend)
     * 
     * @param  int     $orgId
     * @param  string  $orgHash
     * 
     * @return $contact
     */
    public function getFullData(int $orgId, string $orgHash=null, bool $isForcedDB=false, int $page=1, int $size=10)
    {
        $objReturnValue=null;
        try {
            if (!$isForcedDB) {
                //TODO: Get elastic data

            } //End if            

            // Get data from DB
            if (empty($objReturnValue)) {
                $objReturnValue = $this->getFullDataFromDB($orgId, $page, $size);
            } //End if
        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Get Full Customers Data from DB for an Organization (Backend)
     * 
     * @param  int     $orgId
     * 
     * @return $contact
     */
    private function getFullDataFromDB(int $orgId, int $page=1, int $size=10)
    {
        $objReturnValue=null;
        try {
            $contact = $this->model
                ->where('org_id', $orgId)
                ->skip(($page - 1) * $size)
                ->take($size)
                ->get();

            $objReturnValue = $contact;
        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Get Full Data for a Cutomer by Identifier
     * 
     * @param  int     $orgId
     * @param  string  $hash
     * @param  bool    $isForcedDB
     * 
     * @return $objReturnValue
     */
    public function getFullDataByIdentifier(int $orgId, string $hash, bool $isForcedDB=false)
    {
        $objReturnValue=null;
        try {
            if (!$isForcedDB) {

            } //End if

            if (empty($objReturnValue)) {
                $objReturnValue = $this->getFullDataByIdentifierFromDB($orgId, $hash);
            } //End if
        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Get Full Data by DB for a Cutomer by Identifier
     * 
     * @param  int     $orgId
     * @param  string  $hash
     * 
     * @return $contact
     */
    private function getFullDataByIdentifierFromDB(int $orgId, string $hash)
    {
        $objReturnValue=null;
        try {
            $contact = $this->model
                ->where('org_id', $orgId)
                ->where('hash', $hash)
                ->first();

            $objReturnValue = $contact;
        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
