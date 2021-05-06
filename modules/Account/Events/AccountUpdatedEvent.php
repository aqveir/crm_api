<?php

namespace Modules\Account\Events;

use Modules\Account\Models\Account;
use Illuminate\Queue\SerializesModels;

class AccountUpdatedEvent
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
    public function __construct(Account $model)
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
