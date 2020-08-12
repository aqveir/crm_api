<?php

namespace Modules\Core\Models\Privilege;

use Modules\Core\Models\BaseModel as Model;
use Modules\Core\Models\Privilege\Traits\Relationship\PrivilegeRelationship;

/**
 * Privilege Model
 */
class Privilege extends Model
{
    use PrivilegeRelationship;

    public $timestamps = false;

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
        'key', 'display_value', 'description'
    ];

    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'description', 'is_active', 'is_superadmin',
        'created_at', 'updated_at', 'pivot',
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
        $this->table = config('crmomni-migration.table_name.privileges');
    }

} //Class ends
