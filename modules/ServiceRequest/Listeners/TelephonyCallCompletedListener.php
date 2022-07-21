<?php

namespace Modules\ServiceRequest\Listeners;

use Modules\ServiceRequest\Services\CommunicationService;
use Modules\CloudTelephony\Events\Call\TelephonyCallCompletedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TelephonyCallCompletedListener implements ShouldQueue
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
     * @param  TelephonyCallCompletedEvent  $event
     * @return void
     */
    public function handle(TelephonyCallCompletedEvent $event)
    {
        $organization = $event->organization;
        $payload = $event->payload;
        $ipAddress = $event->ipAddress;

        //Create the default user
        $this->service->createDefault($request, $organization);
    } //Function ends

} //Class ends
