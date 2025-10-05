<?php

namespace Modules\User\Events;

use Modules\User\Models\User\User;
use Modules\Core\Models\Organization\Organization;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Organization Model variable
     */
    public $organization;


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
    public function __construct(Organization $organization, User $user, string $ipAddress)
    {
        $this->organization = $organization;
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
