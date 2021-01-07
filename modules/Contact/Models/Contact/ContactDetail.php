<?php 

namespace Modules\Contact\Models\Contact;

use Config;

use Modules\Core\Models\BaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Modules\Contact\Models\Contact\Traits\Relationship\ContactDetailRelationship;

/**
 * Eloquent Contact Details Model
 */
class ContactDetail extends Model {

    use ContactDetailRelationship;
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
        'org_id', 'contact_id', 'type_id', 'subtype_id', 
        'country_id', 'identifier', 
        'is_primary', 'is_verified',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'org_id', 'contact_id', 'type_id', 'subtype_id',
        'country_id', 'pivot', 'identifier', 'is_active',
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
    protected $appends = ['identifier_masked'];


    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['type', 'subtype', 'country'];


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('crmomni-migration.table_name.contact.details');

    }


    public function getIdentifierMaskedAttribute() {
        $objReturnValue=null;
        try {
            $data = $this->attributes['identifier'];
  
            $objReturnValue = $data;
        } catch(Exception $e) {
            $objReturnValue=null;
        } //Try-catch ends
        
        return $objReturnValue;
    } //Function ends
} //Class ends