<?php

namespace Modules\ServiceRequest\Repositories;

use Modules\ServiceRequest\Contracts\{TaskContract};

use Modules\ServiceRequest\Models\Task;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class TaskRepository
 * 
 * @package Module\ServiceRequest\Repositories
 */
class TaskRepository extends EloquentRepository implements TaskContract
{

    /**
     * Repository constructor.
     *
     * @param \Task  $model
     */
    public function __construct(Task $model)
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
    public function getFullData(int $orgId, int $typeId, bool $isForcedDB=false, int $page=1, int $size=10, int $accountId=0, int $ownerId=0)
    {
        $objReturnValue=null;
        try {
            if (!$isForcedDB) {
                //TODO: Get elastic data

            } //End if            

            // Get data from DB
            if (empty($objReturnValue)) {
                $response = $this->getFullDataFromDB($orgId, $typeId, $page, $size);

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
    private function getFullDataFromDB(int $orgId, int $typeId, int $page=1, int $size=10)
    {
        $objReturnValue=null;
        try {
            $data = $this->model
            ->where('org_id', $orgId)
            ->where('type_id', $typeId)
            ->orderBy('updated_at', 'desc')
            ->orderBy('end_at')
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
     * Get Full Data by Identifier
     * 
     * @param  int     $orgId
     * @param  int     $typeId
     * @param  int     $id
     * @param  bool    $isForcedDB
     * 
     * @return $objReturnValue
     */
    public function getFullDataByIdentifier(int $orgId, int $typeId, int $id, bool $isForcedDB=false)
    {
        $objReturnValue=null;
        try {
            if (!$isForcedDB) {

            } //End if

            if (empty($objReturnValue)) {
                $objReturnValue = $this->getFullDataByIdentifierFromDB($orgId, $typeId, $id);
            } //End if
        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Get Full Data from DB by Identifier
     * 
     * @param  int     $orgId
     * @param  int     $typeId
     * @param  int     $id
     * 
     * @return $data
     */
    private function getFullDataByIdentifierFromDB(int $orgId, int $typeId, int $id)
    {
        $objReturnValue=null;
        try {
            $data = $this->model
                ->where('org_id', $orgId)
                ->where('id', $id)
                ->where('type_id', $typeId)
                ->first();

            $objReturnValue = $data;
        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends
	
} //Class ends