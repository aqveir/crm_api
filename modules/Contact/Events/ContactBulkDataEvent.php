<?php

namespace Modules\Contact\Events;

use Illuminate\Queue\SerializesModels;

class ContactBulkDataEvent
{
    use SerializesModels;


    /**
     * Model variable
     */
    public $model;


    /**
     * Data variable
     */
    public $data;


    /**
     * IP Address variable
     */
    public $ipAddress;


    /**
     * Model Created by variable
     */
    public $createdBy;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($model, array $data, string $ipAddress=null, int $createdBy=0)
    {
        $this->model = $model;
        $this->data = $data;
        $this->ipAddress = $ipAddress;
        $this->createdBy = $createdBy;
    } //Function ends


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
