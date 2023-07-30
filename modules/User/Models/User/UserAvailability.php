<?php

namespace Modules\User\Models\User;

use Modules\Core\Models\BaseModel as Model;
use Modules\User\Events\UserAvailabilitySavedEvent;
use Modules\User\Models\User\Traits\Relationship\UserAvailabilityRelationship;

/**
 * User Availability Model
 */
class UserAvailability extends Model {
    use UserAvailabilityRelationship;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;


    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => UserAvailabilitySavedEvent::class
    ];
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'org_id', 'user_id', 'status_id', 'ip_address'
    ];

    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'org_id', 'created_at', 'updated_at'
    ];


    /**
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'last_updated_at'
    ];


    /** The accessors to append to the model's array form.
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
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format(config('aqveir.settings.date_format_response_generic'));
    } //Function ends


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.user.availability');
    } //Function ends

} //Class ends
