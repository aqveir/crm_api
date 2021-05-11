<?php

namespace Modules\ServiceRequest\Policies;

use Modules\User\Models\User\User;
use Modules\ServiceRequest\Models\Task;

use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
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
     * @param  \Modules\ServiceRequest\Models\Task  $task
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
        if ($user->hasPrivileges(['list_all_tasks'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (view) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\ServiceRequest\Models\Task  $task
     * 
     * @return bool
     */
    public function view(User $user, Task $task)
    {
        if ($user->hasPrivileges(['view_task'])) {
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
        if ($user->hasPrivileges(['add_task'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (update) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\ServiceRequest\Models\Task  $task
     * 
     * @return \Illuminate\Auth\Access\Response
     */
    public function update(User $user, Task $task)
    {
        if ($user->hasPrivileges(['edit_task'])) {
            if ($user->hasRoles(['organization_admin'])) {
                return ($user->organization['id'] == $task->organization['id']);
            } else {
                return ($user['id'] == $task->owner['id']);
            } //End if            
        } else {
            return false;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (delete) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\ServiceRequest\Models\Task  $task
     * 
     * @return bool
     */
    public function delete(User $user, Task $task)
    {
        if ($user->hasPrivileges(['delete_task'])) {
            if ($user->hasRoles(['organization_admin'])) {
                return ($user->organization['id'] == $task->organization['id']);
            } else {
                return ($user['id'] == $task->owner['id']);
            } //End if            
        } else {
            return false;
        } //End if
    } //Function ends

} //Class ends

