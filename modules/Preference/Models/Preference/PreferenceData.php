<?php

namespace Modules\Preference\Models\Preference;

use Modules\Core\Models\BaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Modules\Preference\Models\Preference\Traits\Relationship\PreferenceDataRelationship;

/**
 * Eloquent Model for Preference Data
 */
class PreferenceData extends Model {

    use PreferenceDataRelationship;
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
        'org_id', 'key', 'display_value', 'description'
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
        'org_id',
        'created_by', 'updated_by',
        'created_at', 'updated_at'
    ]; 


    /**
     *
     * @var array
     */
    protected $dates = [
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
    protected $with = [];


    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format(config('crmomni.settings.date_format_response_generic'));
    }


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('crmomni-migration.table_name.preference.data');
    }

} //Class ends