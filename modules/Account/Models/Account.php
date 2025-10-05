<?php

namespace Modules\Account\Models;

use Modules\Core\Models\BaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Modules\Account\Models\Traits\Action\AccountAction;
use Modules\Account\Models\Traits\Relationship\AccountRelationship;

/**
 * Eloquent Model for Account
 */
class Account extends Model {

    use AccountAction;
    use AccountRelationship;
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
        'org_id', 'name', 'description', 'type_id', 'owner_id',
        'address', 'locality', 'city', 'state_id', 'country_id', 'zipcode',
        'google_place_id', 'longitude', 'latitude', 'timezone_id',
        'website', 'email', 'phone',
        'is_default'
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
        'org_id', 'description', 'type_id', 'owner_id',
        'address', 'locality', 'city', 'state_id', 'country_id', 'zipcode',
        'google_place_id', 'longitude', 'latitude', 'timezone_id',
        'website', 'email', 'phone', 'is_default',
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
    protected $with = [];
    
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean'
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
        return 'hash';
    }
    

    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.account.main');
    }


    /**
     * Boot function for using with User Events
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model)
        {
            $model->generateHashKey();
        });
    } //Function ends


    /**
     * Generate the Model hash unique identifier that is called
     * on the Model event while creating record.
     * 
     */
    private function generateHashKey() {
        $this->attributes['hash'] = $this->generateRandomHash('ac');
        return !is_null($this->attributes['hash']);
    } //Function ends

} //Class ends