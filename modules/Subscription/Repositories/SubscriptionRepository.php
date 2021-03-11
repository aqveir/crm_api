<?php

namespace Modules\Subscription\Repositories;

use Modules\Subscription\Contracts\{SubscriptionContract};

use Modules\Subscription\Models\Subscription;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class SubscriptionRepository
 * 
 * @package Module\Subscription\Repositories
 */
class SubscriptionRepository extends EloquentRepository implements SubscriptionContract
{

    /**
     * Repository constructor.
     *
     * @param \Subscription  $model
     */
    public function __construct(Subscription $model)
    {
        $this->model = $model;
    }


	/**
	 * Get Latest/Recent Subscription Object
	 */
	public function getRecentNote(int $typeId, int $referenceId)
	{
		$objReturnValue=null;
		
		try {
            $query = $this->model;
	        $query = $query->where('entity_type_id', $typeId);
	        $query = $query->where('reference_id', $referenceId);
	        $query = $query->orderBy('created_at', 'desc');
	        $query = $query->firstOrFail();

	        $objReturnValue = $query;		
		} catch(Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-catch ends
		
		return $objReturnValue;
	} //Function ends
	
} //Class ends