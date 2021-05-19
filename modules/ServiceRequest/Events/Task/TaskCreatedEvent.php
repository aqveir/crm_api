<?php

namespace Modules\ServiceRequest\Events\Task;

use Illuminate\Queue\SerializesModels;
use Modules\ServiceRequest\Models\Task;

class TaskCreatedEvent
{
    use SerializesModels;

    /**
     * Model variable
     */
    public $model;


    /**
     * Is Model Auto Created variable
     */
    public $isAutoCreated;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Task $model, bool $isAutoCreated=false)
    {
        $this->model = $model;
        $this->isAutoCreated = $isAutoCreated;
    }

    
    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
