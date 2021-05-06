<?php

namespace Modules\ServiceRequest\Policies;

use Modules\ServiceRequest\Models\ServiceRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceRequestPolicy
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
