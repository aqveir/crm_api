<?php

namespace Modules\Preference\Events;

use Modules\Preference\Models\Preference\Preference;
use Illuminate\Queue\SerializesModels;

class PreferenceUpdatedEvent
{
    use SerializesModels;

    /**
     * Preference Model variable
     */
    public $preference;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Preference $preference)
    {
        $this->preference = $preference;
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

} //Class ends
