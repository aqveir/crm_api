<?php

namespace Modules\Core\Repositories\Privileges;

use App\Contracts\{PermissionContract};
use Modules\Core\Repositories\RepositoryAbstract;
use Spatie\Permission\Models\Permission;

/**
 * Class PermissionRepository
 * @package Modules\Core\Repositories
 */
class PermissionRepository extends RepositoryAbstract implements PermissionContract
{
    /**
     * @return string
     */
    public function entity()
    {
        return Permission::class;
    }

}
