<?php

namespace Modules\Preference\Policies;

use Modules\User\Models\User\User;
use Modules\Preference\Models\Preference\Preference;

use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class PreferencePolicy
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
        return true;
        if ($user->hasPrivileges(['list_all_organization_preferences'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (show) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Preference\Models\Preference\Preference  $preference
     * 
     * @return bool
     */
    public function view(User $user, Preference $preference)
    {       
        if ($user->hasPrivileges(['view_preference'])) {
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
        if ($user->hasPrivileges(['add_preference'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (update) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Preference\Models\Preference\Preference  $preference
     * 
     * @return \Illuminate\Auth\Access\Response
     */
    public function update(User $user, Preference $preference)
    {
        if ($user->hasPrivileges(['edit_preference'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (delete) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Preference\Models\Preference\Preference  $preference
     * 
     * @return bool
     */
    public function delete(User $user, Preference $preference)
    {
        if ($user->hasPrivileges(['delete_preference'])) {
            return true;
        } //End if
    } //Function ends

} //Class ends
