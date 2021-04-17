<?php

namespace Modules\Preference\Events;

use Modules\Preference\Models\Preference\Preference;
use Illuminate\Queue\SerializesModels;

class PreferenceCreatedEvent
{
    use SerializesModels;

    /**
     * Preference Model variable
     */
    public $preference;


    /**
     * Is User Model Auto Created variable
     */
    public $isAutoCreated;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Preference $preference, bool $isAutoCreated=false)
    {
        $this->preference = $preference;
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

} //Class ends
