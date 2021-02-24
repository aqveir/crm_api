<?php

namespace Modules\Document\Policies;

use Modules\User\Models\User\User;
use Modules\Document\Models\Document;

use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
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
     * @param  \Modules\Document\Models\Document  $document
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
     * Determine if the given action (view) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Document\Models\Document  $document
     * 
     * @return bool
     */
    public function view(User $user, Document $document)
    {
        return true;
        if ($user->hasPrivileges(['add_document'])) {
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
        if ($user->hasPrivileges(['add_document'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (update) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Document\Models\Document  $document
     * 
     * @return \Illuminate\Auth\Access\Response
     */
    public function update(User $user, Document $document)
    {
        if ($user->hasPrivileges(['edit_document'])) {
            if ($user->hasRoles(['organization_admin'])) {
                return ($user->organization['id'] == $document->owner->organization['id']);
            } else {
                return ($user['id'] == $document->owner['id']);
            } //End if            
        } else {
            return false;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (delete) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Document\Models\Document  $document
     * 
     * @return bool
     */
    public function delete(User $user, Document $document)
    {
        if ($user->hasPrivileges(['delete_document'])) {
            if ($user->hasRoles(['organization_admin'])) {
                return ($user->organization['id'] == $document->owner->organization['id']);
            } else {
                return ($user['id'] == $document->owner['id']);
            } //End if            
        } else {
            return false;
        } //End if
    } //Function ends

} //Class ends
