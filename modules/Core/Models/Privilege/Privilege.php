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
        'id', 'description', 'is_active', 'is_secure',
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['last_updated_at'];


    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [];


    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_secure' => 'boolean'
    ];


    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'key';
    }


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.privileges');
    }

} //Class ends
