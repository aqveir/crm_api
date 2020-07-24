<?php

namespace Modules\User\Providers;

use Modules\Core\Events\OrganizationCreatedEvent;
use Modules\User\Listeners\NewOrganizationListener;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrganizationCreatedEvent::class => [
            NewOrganizationListener::class
        ],
    ];
}