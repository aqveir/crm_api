<?php 

namespace Modules\Contact\Models\Contact;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent Model for the Company
 */
class Company extends Model {

    public $timestamps = false;


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
        'id', 'name', 'description'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['description']; 


    /**
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at'
    ];


    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.company');
    }

} //Class ends