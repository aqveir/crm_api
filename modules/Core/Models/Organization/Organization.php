<?php

namespace Modules\Core\Models\Organization;

use Modules\Core\Models\BaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Modules\Core\Models\Organization\Traits\Relationship\OrganizationRelationship;
use Modules\Core\Models\Organization\Traits\Action\OrganizationAction;

use Illuminate\Notifications\Notifiable;

/**
 * Organization Model
 */
class Organization extends Model
{
    use OrganizationRelationship;
    use OrganizationAction;
    use Notifiable;
    use SoftDeletes;

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
        'logo', 'name', 'subdomain', 'industry_id', 'timezone_id',
        'address', 'locality', 'city', 'state_id', 'country_id', 'zipcode',
        'google_place_id', 'longitude', 'latitude',
        'website', 'contact_person_name','email', 'phone', 'phone_idd', 'search_tags'
    ];


    /**
     * Protected attributes that CANNOT be mass assigned.
     *
     * @var array
     */
    protected $guarded = [ 
        'id', 'hash' 
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'subdomain', 'is_active', 'industry_id', 'timezone_id', 'custom_domain',
        'address', 'locality', 'city', 'state_id', 'country_id', 'zipcode',
        'google_place_id', 'longitude', 'latitude', 'logo',
        'website', 'contact_person_name', 'email', 'phone', 'phone_idd', 'search_tags',
        'created_by', 'updated_by', 'deleted_by',
        'created_at', 'updated_at', 'deleted_at', 'last_updated_at'
    ]; 


    /**
     * The attributes that are represented as dates
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
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
        return $date->format(config('crmomni.settings.date_format_response_generic'));
    } //Function ends


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
        $this->table = config('crmomni-migration.table_name.organizations');
    } //Function ends


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
        $this->attributes['hash'] = $this->generateRandomHash('o');
        return !is_null($this->attributes['hash']);
    } //Function ends

} //Class ends
