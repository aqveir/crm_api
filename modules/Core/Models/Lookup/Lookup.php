<?php

namespace Modules\Core\Models\Lookup;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\Lookup\Traits\Relationship\LookupRelationship;

/**
 * Lookup Model
 */
class Lookup extends Model
{
    use LookupRelationship;


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
        'key', 'display_value'
    ];

    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'description', 'pivot', 
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
    protected $appends = [];

    
    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['values'];


    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_editable' => 'boolean',
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
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'key';
    }


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.lookup');
    }

} //Class ends
