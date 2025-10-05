<?php

namespace Modules\Core\Policies;

use Modules\User\Models\User\User;
use Modules\Core\Models\Lookup\Lookup;

use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class LookupPolicy
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
     * @param  \Modules\Core\Models\Lookup\Lookup  $lookup
     * 
     * @return bool
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRoles(['super_admin'])) {
            return true;
        } //End if
    }


    /**
     * Determine if the given action (index) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * 
     * @return bool
     */
    public function viewAny(User $user)
    {
        if ($user) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (show) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Core\Models\Lookup\Lookup  $lookup
     * 
     * @return bool
     */
    public function view(User $user, Lookup $lookup)
    {       
        return true;

        if ($user->hasPrivileges(['view_organization'])) {
            if ($user->hasRoles(['super_admin'])) {
                return true;
            } else {
                return ($lookup['is_editable'] == true);
            } //End if
        } else {
            return false;
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
        if ($user->hasPrivileges(['edit_organization'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (update) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Core\Models\Lookup\Lookup  $lookup
     * 
     * @return bool
     */
    public function update(User $user, Lookup $lookup)
    {
        if ($user->hasPrivileges(['edit_organization'])) {
            if ($user->hasRoles(['super_admin'])) {
                return true;
            } else {
                return ($lookup['is_editable'] == true);
            } //End if
        } else {
            return false;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (delete) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Core\Models\Lookup\Lookup  $lookup
     * 
     * @return bool
     */
    public function delete(User $user, Lookup $lookup)
    {
        if ($user->hasPrivileges(['edit_organization'])) {
            if ($user->hasRoles(['super_admin'])) {
                return true;
            } else {
                return ($lookup['is_editable'] == true);
            } //End if
        } //End if
    } //Function ends

} //Class ends
