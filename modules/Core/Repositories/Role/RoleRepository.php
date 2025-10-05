<?php

namespace Modules\Core\Repositories\Role;

use Modules\Core\Contracts\{RoleContract};

use Modules\Core\Models\Role\Role;
use Modules\Core\Repositories\EloquentRepository;

use Illuminate\Support\Facades\Log;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class RoleRepository
 * @package Modules\Core\Repositories\Role
 */
class RoleRepository extends EloquentRepository implements RoleContract
{

    /**
     * Repository constructor.
     *
     * @param  Role  $model
     */
    public function __construct(Role $model)
    {
        $this->model = $model;
    }


    /**
	 * Get Roles for Organization
	 */
	public function getRolesForOrganization(int $orgId)
	{
		$objReturnValue=null;
		
		try {
            $model = $this->model
                ->with('privileges')
                ->where('org_id', $orgId)
                ->get();

            //Check if the data exists
            if (empty($model)) {
                throw new NotFoundHttpException();
            } //End if

	        $objReturnValue = $model;
		} catch(NotFoundHttpException $e) {
            log::error('UserRepository:getRolesForOrganization:NotFoundHttpException:' . $e->getMessage());
			$objReturnValue=null;
		} catch(Exception $e) {
            log::error('UserRepository:getRolesForOrganization:Exception:' . $e->getMessage());
			$objReturnValue=null;
		} //Try-catch ends
		
		return $objReturnValue;
    } //Function ends


    /**
	 * Get Role by Identifier for Organization
	 */
	public function getRoleByIdForOrganization(int $orgId, string $roleKey)
	{
		$objReturnValue=null;
		
		try {
            $model = $this->model
                ->with('privileges')
                ->where('org_id', $orgId)
                ->where('key', $roleKey)
                ->first();

            //Check if the data exists
            if (empty($model)) {
                throw new NotFoundHttpException();
            } //End if

	        $objReturnValue = $model;
		} catch(NotFoundHttpException $e) {
            log::error('UserRepository:getRoleByIdForOrganization:NotFoundHttpException:' . $e->getMessage());
			$objReturnValue=null;
		} catch(Exception $e) {
            log::error('UserRepository:getRoleByIdForOrganization:Exception:' . $e->getMessage());
			$objReturnValue=null;
		} //Try-catch ends
		
		return $objReturnValue;
    } //Function ends

} //Class ends
