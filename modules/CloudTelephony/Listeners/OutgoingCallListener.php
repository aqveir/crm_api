<?php

namespace Modules\CloudTelephony\Listeners;

use Modules\Contact\Events\ContactCallOutgoingEvent;
use Modules\CloudTelephony\Services\TelephonyVoiceService;

use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OutgoingCallListener
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
    public function __construct(TelephonyVoiceService $service)
    {
        $this->service = $service;
    } //Function ends


    /**
     * Handle the event.
     *
     * @param ContactCallOutgoingEvent $event
     * @return void
     */
    public function handle(ContactCallOutgoingEvent $event)
    {
        $payload = $event->payload;
        $contact = $event->contact;
        $orgHash = $payload['org_hash'];

        //Make an outgoing call
        $this->service->makecall($orgHash, $payload);
    } //Function ends

} //Class ends
