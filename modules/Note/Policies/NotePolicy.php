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
     * Determine if the given note can be created by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Note\Models\Note  $note
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
     * Determine if the given note can be created by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Note\Models\Note  $note
     * 
     * @return bool
     */
    public function create(User $user)
    {
        return $user->id === 1;
    } //Function ends


    /**
     * Determine if the given note can be updated by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Note\Models\Note  $note
     * 
     * @return \Illuminate\Auth\Access\Response
     */
    public function update(User $user, Note $note)
    {
        return ($user->id === $note->created_by);
    } //Function ends


    /**
     * Determine if the given note can be deleted by the user.
     *
     * @param  \Modules\User\Models\User\User  $user
     * @param  \Modules\Note\Models\Note  $note
     * 
     * @return bool
     */
    public function delete(User $user, Note $note)
    {
        return $user->id === $note->created_by;
    } //Function ends

} //Class ends
