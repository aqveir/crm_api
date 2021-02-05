<?php

namespace Modules\Account\Events;

use Modules\Account\Models\Account;
use Illuminate\Queue\SerializesModels;

class AccountCreatedEvent
{
    use SerializesModels;

    /**
     * Account Model variable
     */
    public $account;


    /**
     * Is Account Model Auto Created variable
     */
    public $isAutoCreated;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Account $account, bool $isAutoCreated=false)
    {
        $this->account = $account;
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
