<?php

namespace Modules\ServiceRequest\Events\Communication;

use Illuminate\Queue\SerializesModels;
use Modules\ServiceRequest\Models\Communication;

class SMSCommunicationCreated
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
    public function __construct(Communication $model)
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
