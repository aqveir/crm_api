<?php

namespace Modules\Preference\Models\Preference;

use Modules\Core\Models\BaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Modules\Preference\Models\Preference\Traits\Relationship\PreferenceRelationship;

/**
 * Eloquent Model for Preferences
 */
class Preference extends Model {

    use PreferenceRelationship;
    use SoftDeletes;

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
        'org_id', 'name', 'display_value', 'description', 'column_name',
        'is_minimum', 'is_maximum', 'is_multiple', 'external_url',
        'keywords', 'order', 'type_id', 'data_id', 
        'created_by', 
    ];


    /**
     * Protected attributes that CANNOT be mass assigned.
     *
     * @var array
     */
    protected $guarded = [ 
        'id'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'org_id', 'type_id', 'data_id', 'description',
        'created_by', 'updated_by', 'deleted_by',
        'created_at', 'updated_at', 'deleted_at'
    ]; 


    /**
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
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
    protected $with = ['type', 'data'];

    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_minimum' => 'boolean', 
        'is_maximum' => 'boolean', 
        'is_multiple' => 'boolean',
        'is_active' => 'boolean',
    ];


    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format(config('aqveir.settings.date_format_response_generic'));
    }


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.preference.main');
    }

} //Class ends