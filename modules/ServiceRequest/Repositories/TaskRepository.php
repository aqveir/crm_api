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
	
} //Class ends