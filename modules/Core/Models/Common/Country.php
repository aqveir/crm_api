<?php 

namespace Modules\Core\Models\Common;

use Modules\Core\Models\BaseModel as Model;
use Modules\Core\Models\Common\Traits\Relationship\CountryRelationship;

/**
 * Eloquent Model for the Country
 */
class Country extends Model {

    use CountryRelationship;


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
        'alpha2_code', 'alpha3_code', 'numeric_code', 'iso3166_2_code',
        'display_value', 'display_official_name', 
        'official_domain_extn', 'currency_code', 'phone_idd_code', 
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'numeric_code', 'display_official_name', 
        'official_domain_extn', 'iso3166_2_code', 'currency_code',
        'is_active',
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
    protected $appends = ['last_updated_at'];


    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [];


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.countries');
    }

} //Class ends