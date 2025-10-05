<?php

namespace Modules\ServiceRequest\Events\ServiceRequest;

use Modules\ServiceRequest\Models\ServiceRequest;
use Illuminate\Queue\SerializesModels;

class ServiceRequestUpdatedEvent
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
    public function __construct(ServiceRequest $model)
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
