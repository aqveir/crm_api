<?php

namespace Modules\MailParser\Events;

use Modules\Core\Models\Organization\Organization;

use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;

class MailReceivedEvent
{
    use SerializesModels;


    /**
     * Model variable
     */
    public $organization;


    /**
     * Request Data as Collection variable
     */
    public $request;


    /**
     * Payload Data as Collection variable
     */
    public $payload;


    /**
     * Payload Data as Collection variable
     */
    public $ipAddress;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Organization $organization, Collection $request, Collection $payload, string $ipAddress=null)
    {
        $this->organization = $organization;
        $this->request = $request;
        $this->payload = $payload;
        $this->ipAddress = $ipAddress;
    }

    
    /**
     * Get the channels the event should be broadcast on.
     *, 
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

} //Class ends
