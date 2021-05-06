<?php

namespace Modules\User\Events;

use Modules\User\Models\User\User;
use Illuminate\Queue\SerializesModels;

class UserCreatedEvent
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
    public function __construct(User $model, bool $isAutoCreated=false)
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
