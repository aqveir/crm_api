<?php

namespace Modules\CloudTelephony\Providers;

use Modules\Contact\Events\ContactCallOutgoingEvent;
use Modules\CloudTelephony\Listeners\OutgoingCallListener;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //Contact Call Outgoing Event
        ContactCallOutgoingEvent::class => [
            OutgoingCallListener::class
        ],
    ];


    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
    ];
    
} //Class ends