<?php

namespace Modules\Core\Policies;

use Modules\User\Models\User\User;
use Modules\Core\Models\Privilege\Privilege;

use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrivilegePolicy
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
     * Determine if the given action (index) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * 
     * @return bool
     */
    public function viewAny(User $user)
    {
        if ($user->hasPrivileges(['list_all_privileges'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (show) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Core\Models\Privilege\Privilege  $privilege
     * 
     * @return bool
     */
    public function view(User $user, Privilege $privilege)
    {       
        return false;
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
        if ($user->hasRoles(['super_admin'])) {
            return true;
        } else {
            return false;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (update) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Core\Models\Privilege\Privilege  $privilege
     * 
     * @return bool
     */
    public function update(User $user, Privilege $privilege)
    {
        if ($user->hasRoles(['super_admin'])) {
            return true;
        } else {
            return false;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (delete) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Core\Models\Privilege\Privilege  $privilege
     * 
     * @return bool
     */
    public function delete(User $user, Privilege $privilege)
    {
        if ($user->hasRoles(['super_admin'])) {
            return true;
        } else {
            return false;
        } //End if
    } //Function ends

} //Class ends
