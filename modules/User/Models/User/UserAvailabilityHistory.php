<?php

namespace Modules\User\Models\User;

use Modules\Core\Models\BaseModel as Model;

/**
 * User Availability History Model
 */
class UserAvailabilityHistory extends Model {
    
    const UPDATED_AT = null;
    
    /**
     * The database table used by the model.
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
        'org_id', 'user_id', 'status_id', 'ip_address'
    ];

    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'org_id', 'created_at'
    ];


    /**
     *
     * @var array
     */
    protected $dates = [
        'created_at'
    ];


    /** The accessors to append to the model's array form.
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
        return $date->format(config('aqveir.settings.date_format_response_generic'));
    } //Function ends


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.user.availability_history');
    } //Function ends

} //Class ends
