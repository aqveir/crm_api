<?php 

namespace Modules\Core\Models\Organization;

use Modules\Core\Models\BaseModel as Model;
use Modules\Core\Models\Organization\Traits\Relationship\OrganizationConfigurationRelationship;

/**
 * Eloquent Model for the OrganizationConfiguration
 */
class OrganizationConfiguration extends Model {

    use OrganizationConfigurationRelationship;


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
        'type_id', 'key', 'display_value', 'schema', 'is_active'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'type_id', 'is_active',
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
    protected $with = ['type'];


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.configuration.main');
    }

} //Class ends