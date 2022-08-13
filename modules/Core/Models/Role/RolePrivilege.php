<?php

namespace Modules\Core\Models\Role;

use Modules\Core\Models\BaseModel as Model;
use Modules\Core\Models\Role\Traits\Relationship\RoleRelationship;

/**
 * RolePrivilege Model
 */
class RolePrivilege extends Model
{
    use RoleRelationship;

    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    protected $table;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'role_id', 'privilege_id', 'is_active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'created_at', 'updated_at',
    ];

    /**
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at',
    ];


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.role_privileges');
    } //Function ends
}
