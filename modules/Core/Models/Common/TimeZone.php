<?php 

namespace Modules\Core\Models\Common;

use Modules\Core\Models\BaseModel as Model;
use Modules\Core\Models\Common\Traits\Relationship\TimeZoneRelationship;

/**
 * Eloquent Model for the TimeZone
 */
class TimeZone extends Model {

    use TimeZoneRelationship;


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_id', 'iso3_code', 'display_value', 'gmt_offset',
        'is_dst_enabled', 'dst_start_at', 'dst_end_at',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'country_id', 'is_active',
        'created_by', 'updated_by',
        'created_at', 'updated_at',
    ];


    /**
     *
     * @var array
     */
    protected $dates = [
        'dst_start_at', 'dst_end_at',
        'created_at', 'updated_at'
    ];


    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];


    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['country'];


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.timezones');
    }

} //Class ends