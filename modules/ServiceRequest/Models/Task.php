<?php

namespace Modules\ServiceRequest\Models;

use Modules\Core\Models\BaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Modules\ServiceRequest\Models\Traits\Relationship\TaskRelationship;

/**
 * Eloquent Model for Task
 */
class Task extends Model {

    use TaskRelationship;
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
        'org_id', 'servicerequest_id', 'type_id', 'subtype_id',
        'subject', 'description', 'user_id', 'priority_id', 'status_id',
        'start_at', 'end_at', 'completed_at' 
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
        'org_id', 'servicerequest_id', 'type_id', 'subtype_id',
        'description', 'user_id', 'priority_id', 'status_id',
        'start_at', 'end_at', 'completed_at', 'ip_address',
        'created_by', 'updated_by', 'deleted_by',
        'created_at', 'updated_at', 'deleted_at'
    ]; 


    /**
     *
     * @var array
     */
    protected $dates = [
        'start_at', 'end_at', 'completed_at',
        'created_at', 'updated_at', 'deleted_at', 'last_updated_at'
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
    protected $with = ['priority'];
    
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_scheduled' => 'boolean', 
        'is_completed' => 'boolean',
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
        $this->table = config('aqveir-migration.table_name.service_request.activity');
    }

} //Class ends
