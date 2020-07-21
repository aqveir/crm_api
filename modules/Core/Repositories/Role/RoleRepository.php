<?php

namespace Modules\Core\Repositories\Role;

use App\Contracts\{RoleContract};

use Modules\Core\Models\Role\Role;
use Modules\Core\Repositories\EloquentRepository;

/**
 * Class RoleRepository
 * @package Modules\Core\Repositories
 */
class RoleRepository extends EloquentRepository implements RoleContract
{
    /**
     * @return string
     */
    public function entity()
    {
        return Role::class;
    }

}
