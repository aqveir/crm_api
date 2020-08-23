<?php

namespace Modules\User\Repositories\User;

use Modules\User\Contracts\{UserContract};

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

} //Class ends
