<?php

namespace Modules\User\Events;

use Modules\User\Models\User\User;
use Modules\Core\Models\Organization\Organization;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Organization Model variable
     */
    public $organization;


    /**
     * Model variable
     */
    public $user;


    /**
     * Is Model Auto Created variable
     */
    public $isAutoCreated;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Organization $organization, User $user, bool $isAutoCreated=false)
    {
        $this->organization = $organization;
        $this->user = $user;
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
