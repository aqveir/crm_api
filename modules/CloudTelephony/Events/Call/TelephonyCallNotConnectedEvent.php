<?php

namespace Modules\CloudTelephony\Events\Call;

use Modules\Core\Models\Organization\Organization;

use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;

class TelephonyCallNotConnectedEvent
{
    use SerializesModels;


    /**
     * Organization Model variable
     */
    public $organization;


    /**
     * Payload Data as Collection variable
     */
    public $payload;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Organization $organization, Collection $payload)
    {
        $this->organization = $organization;
        $this->payload = $payload;
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