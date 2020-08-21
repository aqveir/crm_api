<?php

namespace Modules\User\Events;

use Modules\User\Models\User\UserAvailability;
use Illuminate\Queue\SerializesModels;

class UserAvailabilitySavedEvent
{
    use SerializesModels;

    /**
     * User Model variable
     */
    public $userAvailability;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(UserAvailability $userAvailability)
    {
        $this->userAvailability = $userAvailability;
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
