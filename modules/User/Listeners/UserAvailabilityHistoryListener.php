<?php

namespace Modules\User\Listeners;

use Modules\User\Events\UserAvailabilitySavedEvent;
use Modules\User\Repositories\User\UserAvailabilityHistoryRepository;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserAvailabilityHistoryListener
{
    /**
     * Repository
     */
    public $repository;


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UserAvailabilityHistoryRepository $repository)
    {
        $this->repository = $repository;
    } //Function ends


    /**
     * Handle the event.
     *
     * @param \UserAvailabilitySavedEvent $event
     * @return void
     */
    public function handle(UserAvailabilitySavedEvent $event)
    {
        $userAvailability = $event->userAvailability;

        //Create user availability history
        $this->repository->create([
            'org_id' => $userAvailability['org_id'],
            'user_id' => $userAvailability['user_id'],
            'status_id' => $userAvailability['status_id'],
            'ip_address' => $userAvailability['ip_address']
        ]);
    } //Function ends

} //Class ends
