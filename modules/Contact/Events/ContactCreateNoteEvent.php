<?php

namespace Modules\Contact\Events;

use Modules\Contact\Models\Contact\Contact;
use Illuminate\Queue\SerializesModels;

class ContactCreateNoteEvent
{
    use SerializesModels;


    /**
     * Model variable
     */
    public $model;


    /**
     * IP Address variable
     */
    public $ipAddress;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($model, string $ipAddress=null)
    {
        $this->model = $model;
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
