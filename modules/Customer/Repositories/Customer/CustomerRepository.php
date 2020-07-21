<?php

namespace Modules\Customer\Repositories\Customer;

use Modules\Customer\Contracts\{CustomerContract};

use Modules\Customer\Models\Customer\Customer;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class CustomerRepository
 * 
 * @package Modules\Customer\Repositories\Customer
 */
class CustomerRepository extends EloquentRepository implements CustomerContract
{

    /**
     * Repository constructor.
     *
     * @param  Customer  $model
     */
    public function __construct(Customer $model)
    {
        $this->model = $model;
    }


    /**
     * Validate Customer Username
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
     * @return $customer
     */
    public function getFullDataFromDB(int $orgId, string $hash)
    {
        $objReturnValue=null;
        try {
            $customer = $this->model
                ->with(['addresses', 'details', 'orders', 'notes', 'documents'])
                ->where('org_id', $orgId)
                ->where('hash', $hash)
                ->first();

            $objReturnValue = $customer;
        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
