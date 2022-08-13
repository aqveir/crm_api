<?php

namespace Modules\ServiceRequest\Models;

use Modules\Core\Models\BaseModel as Model;

use Modules\ServiceRequest\Models\Traits\Relationship\ServiceRequestPreferenceRelationship;

/**
 * Eloquent Model for ServiceRequest Preferences
 */
class ServiceRequestPreference extends Model {

    use ServiceRequestPreferenceRelationship;

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
        'org_id', 'channel_type_id', 'identifier',
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
        'id', 'org_id', 'channel_type_id', 'pivot',
        'created_by', 'updated_by', 'deleted_by',
        'created_at', 'updated_at', 'deleted_at', 'last_updated_at'
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
    protected $with = ['channel'];
    
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
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
        $this->table = config('aqveir-migration.table_name.service_request.source');
    }

} //Class ends