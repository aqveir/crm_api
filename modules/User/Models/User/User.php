<?php

namespace Modules\User\Models\User;

use Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Modules\Core\Models\BaseModel as Model;
use Modules\User\Models\User\Traits\Relationship\UserRelationship;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * User Model
 */
class User extends Model implements 
    JWTSubject,
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;
    use Notifiable, UserRelationship;
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
        'hash', 'org_id', 'username', 'password',
        'first_name', 'middle_name', 'last_name',
        'country_id', 'timezone_id',
        'email', 'phone', 'virtual_phone_number',
        'failed_attempts', 'is_active', 'last_login_at'
    ];

    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'org_id', 'country_id', 'timezone_id',
        'first_name', 'middle_name', 'last_name',
        'password', 'remember_token', 'is_remote_access_only',
        'is_verified', 'is_active', 'is_pool', 'is_default',
        'failed_attempts', 'max_failed_attempts', 'last_otp',
        'created_by', 'updated_by', 'deleted_by',
        'created_at', 'updated_at', 'deleted_at'
    ];


    /**
     *
     * @var array
     */
    protected $dates = [
        'last_login_at',
        'created_at', 'updated_at', 'deleted_at',
        'last_updated_at'
    ];


    /** The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['name_initials', 'full_name', 'last_updated_at'];


    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['country', 'timezone', 'organization'];


    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format(config('omnichannel.settings.date_format_response_generic'));
    } //Function ends


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('omnicrm-migration.table_name.users');
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
     * Automatically creates hash for the user password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }


    /**
     * Name Initials of user
     * @param string $strReturnValue
     */
    public function getNameInitialsAttribute() {
        $strReturnValue = '';
        $firstChar = '';
        $lastChar = '';
        try {
            $lenRemaining=2;
            $firstName=$this->attributes['first_name'];
            $lastName=$this->attributes['last_name'];
            if ($lastName && strlen($lastName)>0) {
                $lastChar=substr($lastName,0,1);
                $lenRemaining--;
            }
            if ($firstName && strlen($firstName)>0) {
                $firstChar=substr($firstName,0,$lenRemaining);
            }

            $strReturnValue = strtoupper($firstChar.$lastChar);
        } catch (Exception $e) {
            $strReturnValue='';
        }
        return $strReturnValue;
    } //Function ends


    /**
     * Name Full Name of Customer
     * @param string $strReturnValue
     */
    public function getFullNameAttribute() {
        $strReturnValue = '';
        $firstPart = '';
        $lastPart = '';
        $spacer = '';
        try {
            $lenRemaining=2;
            $firstName=$this->attributes['first_name'];
            $lastName=$this->attributes['last_name'];
            if ($lastName && strlen($lastName)>0) {
                $lastPart=$lastName;
            }
            if ($firstName && strlen($firstName)>0) {
                $firstPart=$firstName;
            }

            //Set dilimiter value
            if(strlen($firstPart)>0 && strlen($lastPart)>0) {$spacer=' ';}

            $strReturnValue = $firstPart.$spacer.$lastPart;
        } catch (Exception $e) {
            $strReturnValue='';
        }
        return $strReturnValue;
    } //Function ends


    /**
     * Generate the Model hash unique identifier that is called
     * on the Model event while creating record.
     * 
     */
    private function generateHashKey() {
        $this->attributes['hash'] = $this->generateRandomHash('u');
        return !is_null($this->attributes['hash']);
    } //Function ends


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    } //Function ends


    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    } //Function ends

} //Class ends
