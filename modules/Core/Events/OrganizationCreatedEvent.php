<?php

namespace Modules\Core\Events;

use Modules\Core\Models\Organization\Organization;

use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;

class OrganizationCreatedEvent
{
    use SerializesModels;

    /**
     * Organization Model variable
     */
    public $organization;


    /**
     * Request Data as Collection variable
     */
    public $request;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Organization $organization, Collection $request)
    {
        $this->organization = $organization;
        $this->request = $request;
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
