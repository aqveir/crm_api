<?php

namespace Modules\ServiceRequest\Events\Task;

use Illuminate\Queue\SerializesModels;
use Modules\ServiceRequest\Models\Task;

class TaskDeletedEvent
{
    use SerializesModels;

    /**
     * Model variable
     */
    public $task;

    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
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
