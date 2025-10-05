<?php

namespace Modules\User\Models\Register;

use Modules\Core\Models\BaseModel as Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;

/**
 * User Model
 */
class RegisterUser extends Model {
    use Notifiable;

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
        'first_name', 'middle_name', 'last_name',
        'email', 'phone', 'password',
        'country_id', 'ip_address'
    ];

    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'created_at', 'updated_at'
    ];


    /**
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'verified_at'
    ];


    /** The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];


    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [];


    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format(config('aqveir.settings.date_format_response_generic'));
    } //Function ends


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.user.register');
    } //Function ends


    /**
     * Automatically creates hash for the user password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encrypt($value);
    }


    /**
     * Automatically creates verification token.
     *
     * @param  string  $value
     * @return void
     */
    public function setVerificationTokenAttribute($value)
    {
        $this->attributes['verification_token'] = Str::random(32);
    }

} //Class ends
