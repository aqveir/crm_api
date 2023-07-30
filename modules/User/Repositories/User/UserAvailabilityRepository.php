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
	public function record(int $orgId, int $userId, string $statusKey, string $ipAddress=null)
	{
		$objReturnValue=null;
		
		try {
            //Get status from lookup
            $status = $this->getLookupByKey($statusKey);

            //Record the status
            $model = UserAvailability::updateOrCreate(
                ['user_id' => $userId, 'org_id' => $orgId],
                [
                    'status_id' => $status['id'],
                    'ip_address' => $ipAddress
                ]
            );

	        $objReturnValue = $model;
		} catch(Exception $e) {
            log::error('UserAvailabilityRepository:record:Exception:' . $e->getMessage());
			$objReturnValue=null;
		} //Try-catch ends
		
		return $objReturnValue;
    } //Function ends

} //Class ends
