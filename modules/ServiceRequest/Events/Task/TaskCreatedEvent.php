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
    public $task;


    /**
     * Is Model Auto Created variable
     */
    public $isAutoCreated;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Task $task, bool $isAutoCreated=false)
    {
        $this->task = $task;
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
