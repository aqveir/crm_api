<?php

namespace Modules\User\Providers;

use Modules\User\Events\UserAvailabilitySavedEvent;
use Modules\Core\Events\OrganizationCreatedEvent;

use Modules\User\Listeners\NewOrganizationListener;
use Modules\User\Listeners\UserAvailabilityHistoryListener;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //Organization Created Event
        OrganizationCreatedEvent::class => [
            NewOrganizationListener::class
        ],

        //User Avilability updated Event
        UserAvailabilitySavedEvent::class => [
            UserAvailabilityHistoryListener::class
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