<?php

namespace Modules\Contact\Events;

use Illuminate\Queue\SerializesModels;

class ContactUploadedEvent
{
    use SerializesModels;


    /**
     * Model variable
     */
    public $model;


    /**
     * File variable
     */
    public $file;


    /**
     * Is Model Auto Created variable
     */
    public $isAutoCreated;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($model, $file, bool $isAutoCreated=false)
    {
        $this->model = $model;
        $this->file = $file;
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
    } //Function ends

} //Class ends
