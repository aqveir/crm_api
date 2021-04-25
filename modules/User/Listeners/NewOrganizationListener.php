<?php

namespace Modules\User\Listeners;

use Modules\Core\Events\OrganizationCreatedEvent;
use Modules\User\Services\UserService;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewOrganizationListener
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
    public function __construct(UserService $service)
    {
        $this->service = $service;
    } //Function ends


    /**
     * Handle the event.
     *
     * @param OrganizationCreatedEvent $event
     * @return void
     */
    public function handle(OrganizationCreatedEvent $event)
    {
        $organization = $event->organization;
        $request = $event->request;

        //Create the default user
        $this->service->createDefault($request, $organization);
    } //Function ends

} //Class ends
