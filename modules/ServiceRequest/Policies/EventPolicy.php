<?php

namespace Modules\ServiceRequest\Policies;

use Modules\User\Models\User\User;
use Modules\ServiceRequest\Models\Event as ServiceRequestEvent;

use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * Determine if the given action can be created by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\ServiceRequest\Models\Event as ServiceRequestEvent  $event
     * 
     * @return bool
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRoles(['organization_admin'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (viewAny) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * 
     * @return bool
     */
    public function viewAny(User $user)
    {
        if ($user->hasPrivileges(['list_all_events'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (view) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\ServiceRequest\Models\Event as ServiceRequestEvent  $event
     * 
     * @return bool
     */
    public function view(User $user, ServiceRequestEvent $event)
    {
        if ($user->hasPrivileges(['view_event'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (create) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * 
     * @return bool
     */
    public function create(User $user)
    {
        if ($user->hasPrivileges(['add_event'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (update) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\ServiceRequest\Models\Event as ServiceRequestEvent  $event
     * 
     * @return \Illuminate\Auth\Access\Response
     */
    public function update(User $user, ServiceRequestEvent $event)
    {
        if ($user->hasPrivileges(['edit_event'])) {
            if ($user->hasRoles(['organization_admin'])) {
                return ($user->organization['id'] == $event->organization['id']);
            } else {
                return ($user['id'] == $event->owner['id']);
            } //End if            
        } else {
            return false;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (delete) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\ServiceRequest\Models\Event as ServiceRequestEvent  $event
     * 
     * @return bool
     */
    public function delete(User $user, ServiceRequestEvent $event)
    {
        if ($user->hasPrivileges(['delete_event'])) {
            if ($user->hasRoles(['organization_admin'])) {
                return ($user->organization['id'] == $event->organization['id']);
            } else {
                return ($user['id'] == $event->owner['id']);
            } //End if            
        } else {
            return false;
        } //End if
    } //Function ends

} //Class ends

