<?php

namespace Modules\User\Repositories\User;

use Illuminate\Support\Facades\Log;

use Modules\Core\Traits\LookupAction;
use Modules\User\Models\User\UserAvailability;
use Modules\Core\Repositories\EloquentRepository;

use Exception;
use Modules\Core\Exceptions\ExistingDataException;

/**
 * Class UserAvailabilityRepository
 * @package Modules\User\Repositories\User
 */
class UserAvailabilityRepository extends EloquentRepository
{
    use LookupAction;

    /**
     * Repository constructor.
     *
     * @param  UserAvailability  $model
     */
    public function __construct(UserAvailability $model)
    {
        $this->model = $model;
    }


    /**
	 * Record the User Availability
	 */
	public function record(int $userId, string $statusKey, string $ipAddress=null)
	{
		$objReturnValue=null;
		
		try {
            //Get status from lookup
            $status = $this->getLookupByKey($statusKey);

            //Check if the user/status exists
            $model = UserAvailability::where('user_id', $userId)
                ->where('status_id', $status['id'])
                ->first();

            //Check if the data exists
            if (!empty($model)) {
                throw new ExistingDataException();
            } //End if

            //Record the status
            $model = UserAvailability::updateOrCreate(
                ['user_id' => $userId],
                [
                    'status_id' => $status['id'],
                    'ip_address' => $ipAddress
                ]
            );

	        $objReturnValue = $model;		
		} catch(ExistingDataException $e) {
			log::warning('UserAvailabilityRepository:record:ExistingDataException:' . $e->getMessage());
            throw new ExistingDataException();
		} catch(Exception $e) {
            log::error('UserAvailabilityRepository:record:Exception:' . $e->getMessage());
			$objReturnValue=null;
		} //Try-catch ends
		
		return $objReturnValue;
    } //Function ends

} //Class ends
