<?php

namespace Modules\Note\Policies;

use Modules\User\Models\User\User;
use Modules\Note\Models\Note;

use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotePolicy
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
     * @param  \Modules\Note\Models\Note  $note
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
     * Determine if the given action (create) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * 
     * @return bool
     */
    public function create(User $user)
    {
        if ($user->hasPrivileges(['add_note'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (update) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Note\Models\Note  $note
     * 
     * @return \Illuminate\Auth\Access\Response
     */
    public function update(User $user, Note $note)
    {
        if ($user->hasPrivileges(['edit_note'])) {
            if ($user->hasRoles(['organization_admin'])) {
                return ($user->organization['id'] == $note->owner->organization['id']);
            } else {
                return ($user['id'] == $note->owner['id']);
            } //End if            
        } else {
            return false;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (delete) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Note\Models\Note  $note
     * 
     * @return bool
     */
    public function delete(User $user, Note $note)
    {
        if ($user->hasPrivileges(['delete_note'])) {
            if ($user->hasRoles(['organization_admin'])) {
                return ($user->organization['id'] == $note->owner->organization['id']);
            } else {
                return ($user['id'] == $note->owner['id']);
            } //End if            
        } else {
            return false;
        } //End if
    } //Function ends

} //Class ends
