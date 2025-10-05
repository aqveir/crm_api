<?php

namespace Modules\MailParser\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Contact\Services\Contact\ContactService;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewMailParseListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Contact Service
     */


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ContactService $contactService)
    {
        //
    } //Function ends


    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        Log::info(json_encode($event->request));
    } //Function ends

} //Class ends
