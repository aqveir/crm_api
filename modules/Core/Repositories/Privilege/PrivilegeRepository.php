<?php

namespace Modules\Core\Repositories\Privilege;

use Modules\Core\Contracts\{PrivilegeContract};

use Modules\Core\Models\Privilege\Privilege;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class PrivilegeRepository
 * @package Modules\Core\Repositories\Privilege
 */
class PrivilegeRepository extends EloquentRepository implements PrivilegeContract
{

    /**
     * Repository constructor.
     *
     * @param  Privilege  $model
     */
    public function __construct(Privilege $model)
    {
        $this->model = $model;
    }


    /**
	 * Get Privileges by Names
	 */
	public function getPrivilegesByNames(array $names, string $columnName='id', bool $isActive=null)
	{
		$objReturnValue=null;
		
		try {
            $query = Privilege::whereIn($columnName, $names);
            if (!empty($isActive)) {
                $query = $query->where('is_active', $isActive);
            } //End if
            $query = $query->get();

            //Transform data
            $objReturnValue = $query;	
		} catch(Exception $e) {
			$objReturnValue=null;
			throw new Exception($e);
		} //Try-catch ends
		
		return $objReturnValue;
	} //Function ends

} //Class ends