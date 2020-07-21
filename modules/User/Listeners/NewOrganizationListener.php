<?php

namespace Modules\User\Listeners;

use Modules\Core\Events\OrganizationCreatedEvent;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewOrganizationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param OrganizationCreatedEvent $event
     * @return void
     */
    public function handle(OrganizationCreatedEvent $event)
    {
        //
    }
}
