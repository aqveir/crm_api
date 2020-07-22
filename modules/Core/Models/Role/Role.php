<?php

namespace Modules\Core\Models\Role;

use Modules\Core\Models\BaseModel as Model;
use Modules\Core\Models\Role\Traits\Relationship\RoleRelationship;

/**
 * Role Model
 */
class Role extends Model
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
        'org_id', 'key', 'display_value', 'description'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'org_id', 'pivot', 'created_at', 'updated_at',
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
        $this->table = config('crmomni-migration.table_name.roles');
    }
}
