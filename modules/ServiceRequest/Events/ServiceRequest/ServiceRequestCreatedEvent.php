<?php

namespace Modules\ServiceRequest\Events\ServiceRequest;

use Modules\ServiceRequest\Models\ServiceRequest;
use Illuminate\Queue\SerializesModels;

class ServiceRequestCreatedEvent
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
    public function __construct(ServiceRequest $model, bool $isAutoCreated=false)
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
