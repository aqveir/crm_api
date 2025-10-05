<?php

namespace Modules\Contact\Events;

use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;

use Modules\Contact\Models\Contact\Contact;


class ContactCallOutgoingEvent
{
    use SerializesModels;


    /**
     * Payload variable
     */
    public $payload;

    
    /**
     * Contact variable
     */
    public $contact;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Collection $payload, Contact $contact)
    {
        $this->payload = $payload;
        $this->contact = $contact;
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
