<?php

namespace Modules\Contact\Policies;

use Modules\Core\Policies\BasePolicy;
use Modules\User\Models\User\User;
use Modules\Contact\Models\Contact\Contact;

use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Determine if the given action can be created by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Contact\Models\Contact  $contact
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
        if ($user->hasPrivileges(['list_all_contacts'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (view) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Contact\Models\Contact  $contact
     * 
     * @return bool
     */
    public function view(User $user, Contact $contact)
    {
        if ($user->hasPrivileges(['view_contact'])) {
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
        if ($user->hasPrivileges(['add_contact'])) {
            return true;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (update) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Contact\Models\Contact  $contact
     * 
     * @return \Illuminate\Auth\Access\Response
     */
    public function update(User $user, Contact $contact)
    {
        if ($user->hasPrivileges(['edit_contact'])) {
            if ($user->hasRoles(['organization_admin'])) {
                return ($user->organization['id'] == $contact->organization['id']);
            } else {
                return ($user['id'] == $contact->owner['id']);
            } //End if            
        } else {
            return false;
        } //End if
    } //Function ends


    /**
     * Determine if the given action (delete) can be executed by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Contact\Models\Contact  $contact
     * 
     * @return bool
     */
    public function delete(User $user, Contact $contact)
    {
        if ($user->hasPrivileges(['delete_contact'])) {
            if ($user->hasRoles(['organization_admin'])) {
                return ($user->organization['id'] == $contact->organization['id']);
            } else {
                return ($user['id'] == $contact->owner['id']);
            } //End if            
        } else {
            return false;
        } //End if
    } //Function ends

} //Class ends
