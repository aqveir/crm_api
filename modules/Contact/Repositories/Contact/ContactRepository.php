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
     * Get Full Data for a Cutomer
     * 
     * @param  int     $orgId
     * @param  string  $hash
     * 
     * @return $contact
     */
    public function getFullDataFromDB(int $orgId, string $hash)
    {
        $objReturnValue=null;
        try {
            $contact = $this->model
                ->with(['addresses', 'details', 'orders', 'notes', 'documents'])
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
