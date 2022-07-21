<?php

namespace Modules\ServiceRequest\Listeners;

use Modules\ServiceRequest\Services\CommunicationService;
use Modules\CloudTelephony\Events\Call\TelephonyCallInProgressEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TelephonyCallConnectedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Service
     */
    public $service;


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(CommunicationService $service)
    {
        $this->service = $service;
    } //Function ends


    /**
     * Handle the event.
     *
     * @param  TelephonyCallInProgressEvent  $event
     * @return void
     */
    public function handle(TelephonyCallInProgressEvent $event)
    {
        $organization = $event->organization;
        $payload = $event->payload;
        $ipAddress = $event->ipAddress;

        //Create the default user
        $this->service->createDefault($request, $organization);
    } //Function ends

} //Class ends
