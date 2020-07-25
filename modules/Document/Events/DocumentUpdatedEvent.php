<?php

namespace Modules\Document\Events;

use Modules\Document\Models\Document;
use Illuminate\Queue\SerializesModels;

class DocumentUpdatedEvent
{
    use SerializesModels;


    /**
     * Model variable
     */
    public $document;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
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
