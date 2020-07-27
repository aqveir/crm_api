<?php

namespace Modules\Document\Policies;

use Modules\User\Models\User;
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
        //
    }
}
