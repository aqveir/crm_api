<?php

namespace Modules\ServiceRequest\Models;

use Modules\Core\Models\BaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Modules\ServiceRequest\Models\Traits\Relationship\ServiceRequestEventRelationship;

/**
 * Eloquent Model for ServiceRequestEvent
 */
class ServiceRequestEvent extends Model {

    use ServiceRequestEventRelationship;
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
        'org_id', 
        'type_id', 'subtype_id', 'servicerequest_id', 'subject', 'description',
        'recomendation_id', 'shortlist_status', 'is_scheduled', 'is_completed',
        'assigned_to', 'location', 'external_event_id', 'external_event_source',
        'duration', 'priority_id', 'status_id',
        'scheduled_time', 'start_time', 'end_time', 'completed_time',
        'created_by' 
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
        'id', 'org_id',
        'type_id', 'subtype_id', 'servicerequest_id',
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
    protected $with = ['type'];


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
        $this->table = config('crmomni-migration.table_name.service_request.event');
    }

} //Class ends