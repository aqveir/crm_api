<?php

namespace Modules\User\Events;

use Modules\User\Models\User\User;
use Illuminate\Queue\SerializesModels;

class UserDeletedEvent
{
    use SerializesModels;

    /**
     * User Model variable
     */
    public $user;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
