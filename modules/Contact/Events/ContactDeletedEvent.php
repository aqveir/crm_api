<?php

namespace Modules\Contact\Events;

use Modules\Contact\Models\Contact\Contact;
use Illuminate\Queue\SerializesModels;

class ContactDeletedEvent
{
    use SerializesModels;


    /**
     * Model variable
     */
    public $model;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Contact $model)
    {
        $this->model = $model;
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
