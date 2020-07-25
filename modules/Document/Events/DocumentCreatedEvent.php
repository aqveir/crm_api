<?php

namespace Modules\Document\Events;

use Modules\Document\Models\Document;
use Illuminate\Queue\SerializesModels;

class DocumentCreatedEvent
{
    use SerializesModels;


    /**
     * Model variable
     */
    public $document;


    /**
     * Is Auto Created variable
     */
    public $isAutoCreated;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Document $document, bool $isAutoCreated=false)
    {
        $this->document = $document;
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
