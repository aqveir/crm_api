<?php 

namespace Modules\Core\Models\Common;

use Modules\Core\Models\BaseModel as Model;
use Modules\Core\Models\Common\Traits\Relationship\ConfigurationRelationship;

/**
 * Eloquent Model for the Configuration
 */
class Configuration extends Model {

    use ConfigurationRelationship;


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
        'type_id', 'key', 'display_value', 'schema', 'is_active'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'type_id', 'is_active',
        'created_by', 'updated_by',
        'created_at', 'updated_at',
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
    protected $appends = ['last_updated_at', 'schema'];


    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['type'];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.configuration.main');
    }


    /**
     * Assessor
     */
    public function getSchemaAttribute()
    {
        return json_decode($this->attributes['schema']);
    }

} //Class ends