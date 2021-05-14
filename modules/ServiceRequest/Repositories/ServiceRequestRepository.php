<?php

namespace Modules\ServiceRequest\Repositories;

use Modules\ServiceRequest\Contracts\{ServiceRequestContract};

use Modules\ServiceRequest\Models\ServiceRequest;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class ServiceRequestRepository
 * 
 * @package Modules\ServiceRequest\Repositories
 */
class ServiceRequestRepository extends EloquentRepository implements ServiceRequestContract
{

    /**
     * Repository constructor.
     *
     * @param  ServiceRequest  $model
     */
    public function __construct(ServiceRequest $model)
    {
        $this->model = $model;
    }


    /**
     * Get Full Data for an Organization (Backend)
     * 
     * @param  int     $orgId
     * @param  string  $orgHash
     * 
     * @return $objReturnValue
     */
    public function getFullData(int $orgId, int $categoryId, bool $isForcedDB=false, int $page=1, int $size=10, int $accountId=0, int $ownerId=0)
    {
        $objReturnValue=null;
        try {
            if (!$isForcedDB) {
                //TODO: Get elastic data

            } //End if            

            // Get data from DB
            if (empty($objReturnValue)) {
                $response = $this->getFullDataFromDB($orgId, $categoryId, $page, $size);

                //Account Condition
                if ($accountId>0) {
                    $response->where('account_id', $accountId)->get();
                } //End if

                //Owner Condition
                if ($ownerId>0) {
                    $response->where('owner_id', $ownerId)->get();
                } //End if

                $objReturnValue = $response;
            } //End if
        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Get All Data from DB for an Organization (Backend)
     * 
     * @param  int     $orgId
     * 
     * @return $data
     */
    private function getFullDataFromDB(int $orgId, int $categoryId, int $page=1, int $size=10)
    {
        $objReturnValue=null;
        try {
            $data = $this->model
            ->where('org_id', $orgId)
            ->where('category_id', $categoryId)
            ->orderBy('updated_at', 'desc')
            ->skip(($page - 1) * $size)
            ->take($size)
            ->get();

            $objReturnValue = $data;
        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Get Full Data for a ServiceRequest by Identifier
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
     * Get Full Data by DB for a ServiceRequest by Identifier
     * 
     * @param  int     $orgId
     * @param  string  $hash
     * 
     * @return $data
     */
    private function getFullDataByIdentifierFromDB(int $orgId, string $hash)
    {
        $objReturnValue=null;
        try {
            $data = $this->model
                ->where('org_id', $orgId)
                ->where('hash', $hash)
                ->first();

            $objReturnValue = $data;
        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
