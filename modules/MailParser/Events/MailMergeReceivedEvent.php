<?php

namespace Modules\MailParser\Events;

use Modules\Core\Models\Organization\Organization;

use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;

class MailMergeReceivedEvent
{
    use SerializesModels;


    /**
     * Model variable
     */
    public $organization;


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
    public function __construct(Organization $organization, Collection $payload, string $ipAddress=null)
    {
        $this->organization = $organization;
        $this->payload = $payload;
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

} //Class ends
