<?php

namespace Modules\User\Repositories\User;

use Modules\User\Contracts\{UserContract};

use Modules\Core\Traits\LookupAction;
use Modules\User\Models\User\User;
use Modules\Core\Repositories\EloquentRepository;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UserRepository
 * @package Modules\User\Repositories\User
 */
class UserRepository extends EloquentRepository implements UserContract
{
    use LookupAction;

    /**
     * Repository constructor.
     *
     * @param  User  $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }


    /**
	 * Get User By Hash
	 */
	public function getUserByHash(string $hash)
	{
		$objReturnValue=null;
		
		try {
            //Check if the user/status exists
            $model = $this->model
                ->where('hash', $hash)
                ->first();

            //Check if the data exists
            if (empty($model)) {
                throw new NotFoundHttpException();
            } //End if

	        $objReturnValue = $model;
		} catch(NotFoundHttpException $e) {
            log::error('UserRepository:getUserByHash:NotFoundHttpException:' . $e->getMessage());
			$objReturnValue=null;
		} catch(Exception $e) {
            log::error('UserRepository:getUserByHash:Exception:' . $e->getMessage());
			$objReturnValue=null;
		} //Try-catch ends
		
		return $objReturnValue;
    } //Function ends


    /**
	 * Get User By Attribute
	 */
	public function getUserByAttribute(int $orgId, string $columnName, $columnValue)
	{
		$objReturnValue=null;
		
		try {
            //Check if the user/status exists
            $model = $this->model
                ->where('org_id', $orgId)
                ->where('is_active', true)
                ->where($columnName, $columnValue)
                ->first();

            //Check if the data exists
            if (empty($model)) {
                throw new NotFoundHttpException();
            } //End if

	        $objReturnValue = $model;
		} catch(NotFoundHttpException $e) {
            log::error('UserRepository:getUserByHash:NotFoundHttpException:' . $e->getMessage());
			$objReturnValue=null;
		} catch(Exception $e) {
            log::error('UserRepository:getUserByHash:Exception:' . $e->getMessage());
			$objReturnValue=null;
		} //Try-catch ends
		
		return $objReturnValue;
    } //Function ends


    /**
	 * Record the User Availability
	 */
	public function getRecordsByStatus(int $orgId, string $statusKey, string $roleKey)
	{
		$objReturnValue=null;
		
		try {
            //Get status from lookup
            $status = $this->getLookupByKey($statusKey);

            //Check data
            $model = User::with(['availability', 'roles'])
                ->where('org_id', $orgId)
                ->where('is_active', true)
                ->whereHas('availability.status', function ($inner_query) use ($statusKey) {
                    $inner_query->where('key', $statusKey);
                })
                ->whereHas('roles', function ($inner_query) use ($roleKey) {
                    $inner_query->where('key', $roleKey);
                })
                ->inRandomOrder()
                ->get();

	        $objReturnValue = $model;		
		} catch(ExistingDataException $e) {
			log::warning('UserAvailabilityRepository:getRecordsByStatus:ExistingDataException:' . $e->getMessage());
            throw new ExistingDataException();
		} catch(Exception $e) {
            log::error('UserAvailabilityRepository:getRecordsByStatus:Exception:' . $e->getMessage());
			$objReturnValue=null;
		} //Try-catch ends
		
		return $objReturnValue;
    } //Function ends

} //Class ends
