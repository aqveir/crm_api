<?php

namespace Modules\ServiceRequest\Policies;

use Modules\User\Models\User\User;
use Modules\ServiceRequest\Models\ServiceRequest;

use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceRequestPolicy
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
     * @param  \Modules\ServiceRequest\Models\ServiceRequest  $servicerequest
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
        if ($user->hasPrivileges(['list_all_servicerequests'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (view) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\ServiceRequest\Models\ServiceRequest  $servicerequest
     * 
     * @return bool
     */
    public function view(User $user, ServiceRequest $servicerequest)
    {
        if ($user->hasPrivileges(['view_servicerequest'])) {
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
        if ($user->hasPrivileges(['add_servicerequest'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (update) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\ServiceRequest\Models\ServiceRequest  $servicerequest
     * 
     * @return \Illuminate\Auth\Access\Response
     */
    public function update(User $user, ServiceRequest $servicerequest)
    {
        if ($user->hasPrivileges(['edit_servicerequest'])) {
            if ($user->hasRoles(['organization_admin'])) {
                return ($user->organization['id'] == $servicerequest->organization['id']);
            } else {
                return ($user['id'] == $servicerequest->owner['id']);
            } //End if            
        } else {
            return false;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (delete) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\ServiceRequest\Models\ServiceRequest  $servicerequest
     * 
     * @return bool
     */
    public function delete(User $user, ServiceRequest $servicerequest)
    {
        if ($user->hasPrivileges(['delete_servicerequest'])) {
            if ($user->hasRoles(['organization_admin'])) {
                return ($user->organization['id'] == $servicerequest->organization['id']);
            } else {
                return ($user['id'] == $servicerequest->owner['id']);
            } //End if            
        } else {
            return false;
        } //End if
    } //Function ends

} //Class ends

