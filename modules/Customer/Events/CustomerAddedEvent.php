<?php

namespace Modules\Customer\Events;

use Modules\Customer\Models\Customer\Customer;
use Illuminate\Queue\SerializesModels;

class CustomerAddedEvent
{
    use SerializesModels;

    
    /**
     * Customer variable
     */
    public $customer;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
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
