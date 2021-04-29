<?php

namespace Modules\Core\Events;

use Modules\Core\Models\Organization\Organization;

use Illuminate\Queue\SerializesModels;

class OrganizationDeletedEvent
{
    use SerializesModels;

    /**
     * Organization Model variable
     */
    public $organization;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
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
