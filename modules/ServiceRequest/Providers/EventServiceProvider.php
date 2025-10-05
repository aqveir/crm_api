<?php

namespace Modules\ServiceRequest\Providers;

use Modules\CloudTelephony\Events\Call\TelephonyCallInProgressEvent;
use Modules\CloudTelephony\Events\Call\TelephonyCallCompletedEvent;
use Modules\CloudTelephony\Events\Call\TelephonyCallNotConnectedEvent;

use Modules\ServiceRequest\Listeners\TelephonyCallConnectedListener;
use Modules\ServiceRequest\Listeners\TelephonyCallCompletedListener;
use Modules\ServiceRequest\Listeners\TelephonyCallNotConnectedListener;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //Telephony Call in Progress Event
        TelephonyCallInProgressEvent::class => [
            TelephonyCallConnectedListener::class
        ],

        //Telephony Call Completed Event
        TelephonyCallCompletedEvent::class => [
            TelephonyCallCompletedListener::class
        ],

        //Telephony Call Not Completed Event
        TelephonyCallNotConnectedEvent::class => [
            TelephonyCallNotConnectedListener::class
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