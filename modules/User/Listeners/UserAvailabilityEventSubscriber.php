<?php

namespace Modules\User\Listeners;

use Modules\User\Services\UserAvailabilityService;
use Illuminate\Contracts\Queue\ShouldQueue;

use Exception;

class UserAvailabilityEventSubscriber implements ShouldQueue
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
    public function __construct(UserAvailabilityService $service)
    {
        $this->service = $service;
    } //Function ends


    /**
     * Handle user login events.
     */
    public function handleUserLogin($event) {
        try {
            //Collect data
            $organization = $event->organization;
            $user = $event->user;
            $statusKey = 'user_status_online';
            $ipAddress = $event->ipAddress;

            //Execute service
            $response = $this->service->record($organization['id'], $user['id'], $statusKey, $ipAddress);
        } catch(Exception $e) {
            //Do nothing
        } //Try-catch ends
    } //Function ends


    /**
     * Handle user logout events.
     */
    public function handleUserLogout($event) {
        try {
            //Collect data
            $organization = $event->organization;
            $user = $event->user;
            $statusKey = 'user_status_offline';
            $ipAddress = $event->ipAddress;

            //Execute service
            $response = $this->service->record($organization['id'], $user['id'], $statusKey, $ipAddress);
        } catch(Exception $e) {
            //Do nothing
        } //Try-catch ends
    } //Function ends


    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Modules\User\Events\UserLoginEvent',
            'Modules\User\Listeners\UserAvailabilityEventSubscriber@handleUserLogin'
        );

        $events->listen(
            'Modules\User\Events\UserLogoutEvent',
            'Modules\User\Listeners\UserAvailabilityEventSubscriber@handleUserLogout'
        );

        $events->listen(
            'Modules\User\Events\UserDeletedEvent',
            'Modules\User\Listeners\UserAvailabilityEventSubscriber@handleUserLogout'
        );
    } //Function ends

} //Class ends