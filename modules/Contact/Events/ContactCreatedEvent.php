<?php

namespace Modules\Contact\Events;

use Modules\Contact\Models\Contact\Contact;
use Illuminate\Queue\SerializesModels;

class ContactCreatedEvent
{
    use SerializesModels;


    /**
     * Model variable
     */
    public $model;


    /**
     * Is Model Auto Created variable
     */
    public $isAutoCreated;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Contact $model, bool $isAutoCreated=false)
    {
        $this->model = $model;
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
