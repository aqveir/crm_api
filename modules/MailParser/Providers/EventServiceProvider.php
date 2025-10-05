<?php

namespace Modules\MailParser\Providers;

use Modules\MailParser\Events\MailReceivedEvent;
use Modules\MailParser\Listeners\NewMailParseListener;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //New Mail Received Event
        MailReceivedEvent::class => [
            NewMailParseListener::class
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