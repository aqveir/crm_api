<?php

namespace Modules\ServiceRequest\Repositories;

use Modules\ServiceRequest\Contracts\{EventContract};

use Modules\ServiceRequest\Models\Event as ServiceRequestEvent;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class EventRepository
 * 
 * @package Module\ServiceRequest\Repositories
 */
class EventRepository extends EloquentRepository implements EventContract
{

    /**
     * Repository constructor.
     *
     * @param \ServiceRequestEvent  $model
     */
    public function __construct(ServiceRequestEvent $model)
    {
        $this->model = $model;
    }
	
} //Class ends