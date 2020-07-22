<?php 

namespace Modules\Customer\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Modules\Customer\Models\Customer\Traits\Relationship\CustomerAddressRelationship;
use Modules\Core\Models\Common\Traits\Relationship\MapCountryDataRelationship;

/**
 * Eloquent Customer Address Model
 */
class CustomerAddress extends Model {

    use MapCountryDataRelationship;
    use CustomerAddressRelationship;

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
        'type_id', 'apartment_id', 'society_id', 
        'address1', 'address2', 'locality', 'city', 
        'state_id', 'country_id', 'zipcode', 
        'google_place_id', 'longitude', 'latitude',
        'is_default', ''
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'org_id', 'customer_id', 
        'type_id', 'apartment_id', 'society_id',
        'state_id', 'country_id', 'google_place_id',
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
    protected $appends = [];


    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['apartment', 'society', 'country'];


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('crmomni-migration.table_name.customer.addresses');
    }

} //Class ends