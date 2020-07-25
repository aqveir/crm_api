<?php

namespace Modules\Note\Events;

use Modules\Note\Models\Note;
use Illuminate\Queue\SerializesModels;

class NoteCreatedEvent
{
    use SerializesModels;

    /**
     * Note Model variable
     */
    public $note;


    /**
     * Is Auto Created variable
     */
    public $isAutoCreated;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Note $note, bool $isAutoCreated=false)
    {
        $this->note = $note;
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
