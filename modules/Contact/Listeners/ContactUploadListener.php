<?php

namespace Modules\Contact\Listeners;

use Modules\Contact\Events\ContactUploadedEvent;
use Modules\Contact\Services\Contact\ContactFileService;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactUploadListener
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
    public function __construct(ContactFileService $service)
    {
        $this->service = $service;
    }


    /**
     * Handle the event.
     *
     * @param ContactUploadedEvent $event
     * @return void
     */
    public function handle(ContactUploadedEvent $event)
    {
        $organization = $event->model;
        $files = $event->files;
        $ipAddress = $event->ipAddress;
        $isAutoCreated = $event->isAutoCreated;

        //Create the default user
        $this->service->processUpload($organization['hash'], $files, $ipAddress, $isAutoCreated);
    } //Function ends

} //Class ends
