<?php

namespace Modules\User\Events;

use Modules\User\Models\User\User;
use Illuminate\Queue\SerializesModels;

class UserLogoutEvent
{
    use SerializesModels;

    /**
     * User Model variable
     */
    public $user;


    /**
     * IP Address
     */
    public $ipAddress;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, string $ipAddress)
    {
        $this->user = $user;
        $this->ipAddress = $ipAddress;
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
