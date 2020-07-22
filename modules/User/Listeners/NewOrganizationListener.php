<?php

namespace Modules\User\Listeners;

use Modules\Core\Events\OrganizationCreatedEvent;
use Modules\User\Services\User\UserService;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewOrganizationListener
{
    /**
     * User Service
     */
    public $userService;


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
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
        $this->userService->createDefault($request, $organization['id']);
    } //Function ends

} //Class ends
