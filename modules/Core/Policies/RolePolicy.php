<?php

namespace Modules\Core\Policies;

use Modules\User\Models\User\User;
use Modules\Core\Models\Role\Role;

use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
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
     * @param  \Modules\Core\Models\Role\Role  $role
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
        if ($user->hasPrivileges(['list_all_organizations'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (show) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Core\Models\Role\Role  $role
     * 
     * @return bool
     */
    public function view(User $user, Role $role)
    {       
        if ($user->hasRoles(['super_admin'])) {
            return true;
        } elseif ($user->hasRoles(['organization_admin']) || $user->hasPrivileges(['read_organization_data'])) {
            return $user->organization['id'] == $role['id'];
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
        if ($user->hasPrivileges(['add_organization_data'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (update) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Core\Models\Role\Role  $role
     * 
     * @return bool
     */
    public function update(User $user, Role $role)
    {
        if ($user->hasRoles(['super_admin'])) {
            return true;
        } elseif ($user->hasRoles(['organization_admin']) || $user->hasPrivileges(['edit_organization_data'])) {
            return $user->organization['id'] == $role['id'];
        } else {
            return false;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (delete) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Core\Models\Role\Role  $role
     * 
     * @return bool
     */
    public function delete(User $user, Role $role)
    {
        if ($user->hasRoles(['delete_organization_data'])) {
            return true;
        } //End if
    } //Function ends

} //Class ends
