<?php

namespace Modules\Note\Events;

use Modules\Note\Models\Note;
use Illuminate\Queue\SerializesModels;

class NoteUpdatedEvent
{
    use SerializesModels;

    /**
     * Note Model variable
     */
    public $note;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Note $note)
    {
        $this->note = $note;
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
