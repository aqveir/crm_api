<?php

namespace Modules\Core\Repositories\Role;

use Modules\Core\Contracts\{RoleContract};

use Modules\Core\Models\Role\Role;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class RoleRepository
 * @package Modules\Core\Repositories\Role
 */
class RoleRepository extends EloquentRepository implements RoleContract
{

    /**
     * Repository constructor.
     *
     * @param  Role  $model
     */
    public function __construct(Role $model)
    {
        $this->model = $model;
    }

} //Class ends
