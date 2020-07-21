<?php

namespace Modules\Core\Repositories\User;

use Modules\Core\Contracts\{UserContract};

use Modules\Core\Models\User\User;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class UserRepository
 * @package Modules\Core\Repositories\User
 */
class UserRepository extends EloquentRepository implements UserContract
{

    /**
     * UserRepository constructor.
     *
     * @param  User  $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

} //Class ends
