<?php

namespace Modules\Core\Policies;

use Modules\User\Models\User\User;
use Modules\Core\Models\Organization\Organization;

use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy
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
     * Determine if the given note can be created by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Core\Models\Organization\Organization  $organization
     * 
     * @return bool
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRoles(['organization_admin'])) {
            return true;
        } //End if
    }


    /**
     * Determine if the given organization can be created by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * 
     * @return bool
     */
    public function viewAny(User $user)
    {
        if ($user->hasRoles(['super_admin'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given organization can be created by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * 
     * @return bool
     */
    public function view(User $user, Organization $organization)
    {       
        if ($user->hasRoles(['super_admin'])) {
            return true;
        } elseif ($user->hasRoles(['organization_admin'])) {
            return $user->organization['id'] == $organization['id'];
        } else {
            return false;
        } //End if
    } //Function ends


    /**
     * Determine if the given organization can be created by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * 
     * @return bool
     */
    public function create(User $user)
    {
        if ($user->hasRoles(['super_admin'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given organization can be updated by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Core\Models\Organization\Organization  $organization
     * 
     * @return \Illuminate\Auth\Access\Response
     */
    public function update(User $user, Organization $organization)
    {
        if ($user->hasRoles(['super_admin'])) {
            return true;
        } elseif ($user->hasRoles(['organization_admin'])) {
            return $user->organization['id'] == $organization['id'];
        } else {
            return false;
        } //End if
    } //Function ends


    /**
     * Determine if the given organization can be deleted by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Core\Models\Organization\Organization  $organization
     * 
     * @return bool
     */
    public function delete(User $user, Organization $organization)
    {
        if ($user->hasRoles(['super_admin'])) {
            return true;
        } //End if
    } //Function ends
}
