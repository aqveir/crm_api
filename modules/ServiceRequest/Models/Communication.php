<?php

namespace Modules\ServiceRequest\Models;

use Modules\Core\Models\BaseModel as Model;

use Modules\ServiceRequest\Models\Traits\Relationship\CommunicationRelationship;

/**
 * Eloquent Model for Communication
 */
class Communication extends Model {

    use CommunicationRelationship;

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
        'org_id', 'servicerequest_id', 'activity_subtype_id', 'direction_id',
        'external_uuid', 'start_at', 'end_at',
        'from_person_type_id', 'from_person_identifier_id',
        'to_person_type_id', 'to_initiator_identifier_id',
        'call_from', 'call_to', 'call_status_id', 'call_duration', 'call_recording_url',
        'sms_from', 'sms_to', 'sms_message',
        'email_from', 'email_to', 'email_cc', 'email_subject', 'email_body'
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
        'id', 'org_id', 'servicerequest_id', 'activity_subtype_id', 'direction_id',
        'external_uuid', 'start_at', 'end_at',
        'from_person_type_id', 'from_person_identifier_id',
        'to_person_type_id', 'to_initiator_identifier_id',
        'call_from', 'call_to', 'call_status_id', 'call_duration', 'call_recording_url',
        'sms_from', 'sms_to', 'sms_message',
        'email_from', 'email_to', 'email_cc', 'email_subject', 'email_body',
        'created_by', 'updated_by', 'deleted_by',
        'created_at', 'updated_at', 'deleted_at', 'last_updated_at',
        'ip_address'
    ]; 


    /**
     *
     * @var array
     */
    protected $dates = [
        'start_at', 'end_at',
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
    protected $with = ['type', 'direction'];
    
    
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
        $this->table = config('aqveir-migration.table_name.service_request.communication');
    }

} //Class ends