<?php

namespace Modules\User\Models\User;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\User\Traits\Relationship\UserRelationship;

/**
 * UserRole Model
 */
class UserRole extends Model
{
    use UserRelationship;
    
    public $timestamps = false;


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
      'user_id', 'role_id', 'description',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id', 'role_id', 'description',
    ];  


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.user_roles');
    }

}
