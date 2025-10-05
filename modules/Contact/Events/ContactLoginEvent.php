<?php

namespace Modules\Contact\Events;

use Modules\Contact\Models\Contact\Contact;
use Illuminate\Queue\SerializesModels;

class ContactLoginEvent
{
    use SerializesModels;

    
    /**
     * Contact variable
     */
    public $contact;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Contact $contact)
    {
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
}
