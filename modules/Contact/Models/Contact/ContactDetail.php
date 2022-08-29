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
        'org_id', 'contact_id', 'type_id', 'subtype_id', 'identifier', 
        'is_primary', 'is_verified',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'org_id', 'contact_id', 'type_id', 'subtype_id',
        'pivot', 'is_active',
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
    protected $with = ['type', 'subtype'];
    
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_primary' => 'boolean', 
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];
    

    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.contact.details');

    }

} //Class ends