<?php

namespace Modules\Contact\Listeners;

use Modules\Contact\Events\ContactBulkDataEvent;
use Modules\Contact\Services\Contact\ContactService;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactBulkDataListener
{
    /**
     * Service
     */
    public $service;


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ContactService $service)
    {
        $this->service = $service;
    } //Function ends


    /**
     * Handle the event.
     *
     * @param ContactBulkDataEvent $event
     * @return void
     */
    public function handle(ContactBulkDataEvent $event)
    {
        $organization = $event->model;
        $data = $event->data;
        $ipAddress = $event->ipAddress;
        $createdBy = $event->createdBy;

        //Create the default user
        $this->service->processBulkData($organization, $data, $ipAddress, $createdBy);
    } //Function ends

} //Class ends
