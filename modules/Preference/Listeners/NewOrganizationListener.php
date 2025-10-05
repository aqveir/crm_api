<?php

namespace Modules\Preference\Listeners;

use Modules\Core\Events\OrganizationCreatedEvent;
use Modules\Preference\Services\PreferenceService;

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
    public function __construct(PreferenceService $service)
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
        
        //Create the default preferences
        $this->service->createDefault($request, $organization);
    } //Function ends

} //Class ends
