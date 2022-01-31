<?php

namespace Modules\MailParser\Providers;

use Modules\MailParser\Events\NewMailReceivedEvent;

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
        NewMailReceivedEvent::class => [

        ],
    ];


    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        'Modules\User\Listeners\UserAvailabilityEventSubscriber',
    ];
    
} //Class ends