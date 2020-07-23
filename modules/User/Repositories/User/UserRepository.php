<?php

namespace Modules\User\Repositories\User;

use Modules\User\Contracts\{UserContract};

use Modules\User\Models\User\User;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class UserRepository
 * @package Modules\User\Repositories\User
 */
class UserRepository extends EloquentRepository implements UserContract
{

    /**
     * Repository constructor.
     *
     * @param  User  $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

} //Class ends
