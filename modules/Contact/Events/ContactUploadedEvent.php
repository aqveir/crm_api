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
    public $files;


    /**
     * Is Model Auto Created variable
     */
    public $isAutoCreated;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($model, $files, bool $isAutoCreated=false)
    {
        $this->model = $model;
        $this->files = $files;
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
