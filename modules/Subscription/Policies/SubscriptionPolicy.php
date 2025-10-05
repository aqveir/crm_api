<?php

namespace Modules\Subscription\Policies;

use Modules\User\Models\User\User;
use Modules\Subscription\Models\Subscription;

use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }


    /**
     * Determine if the given action can be created by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Subscription\Models\Subscription  $subscription
     * 
     * @return bool
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRoles(['super_admin'])) {
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
    } //Function ends


    /**
     * Determine if the given action (show) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Subscription\Models\Subscription  $subscription
     * 
     * @return bool
     */
    public function view(User $user, Subscription $subscription)
    {       
        return true;
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
        if ($user->hasPrivileges(['add_subscription'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (update) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Subscription\Models\Subscription  $subscription
     * 
     * @return \Illuminate\Auth\Access\Response
     */
    public function update(User $user, Subscription $subscription)
    {
        if ($user->hasPrivileges(['edit_subscription'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (delete) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Subscription\Models\Subscription  $subscription
     * 
     * @return bool
     */
    public function delete(User $user, Subscription $subscription)
    {
        if ($user->hasPrivileges(['delete_subscription'])) {
            return true;
        } //End if
    } //Function ends

} //Class ends
