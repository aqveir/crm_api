<?php

namespace Modules\User\Models\User;

use Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Modules\Core\Models\BaseModel as Model;
use Modules\User\Models\User\Traits\Action\UserAction;
use Modules\User\Models\User\Traits\Relationship\UserRelationship;

use Illuminate\Support\Str;
use Illuminate\Auth\Authenticatable;
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
    use Authenticatable, Authorizable, CanResetPassword;
    use UserRelationship, UserAction;
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
        'org_id', 'username', 'password',
        'first_name', 'middle_name', 'last_name',
        'email', 'phone', 'language',
        'virtual_phone_number', 'timezone_id',
        'is_remote_access_only', 'failed_attempts',
        'verification_token', 'is_verified',
        'last_login_at', 'created_by'
    ];

    
    /**
     * Protected attributes that CANNOT be mass assigned.
     *
     * @var array
     */
    protected $guarded = [ 
        'id'
    ];

    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'org_id', 'timezone_id', 'avatar',
        'first_name', 'middle_name', 'last_name', 'name_initials',
        'username', 'password', 'remember_token', 'is_remote_access_only',
        'email', 'phone', 'virtual_phone_number',
        'is_active', 'is_pool', 'is_default', 'language',
        'failed_attempts', 'max_failed_attempts', 'mfa_secret',
        'verification_token', 'is_verified',
        'verified_at', 'last_login_at', 'last_updated_at',
        'created_by', 'updated_by', 'deleted_by', 
        'created_at', 'updated_at', 'deleted_at'
    ];


    /**
     *
     * @var array
     */
    protected $dates = [
        'last_login_at', 'verified_at',
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
    protected $with = [];
    
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_remote_access_only' => 'boolean', 
        'is_default' => 'boolean', 
        'is_pool' => 'boolean',
        'is_active' => 'boolean'
    ];


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
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'hash';
    }


    /**
     * Get the phone number in E164 format
     */
    public function getPhoneAttribute($value) {
        if (!empty($value)) {
            $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            
            //Parse phone number
            $phoneNumberObject = $phoneUtil->parse($value, null);

            //Format phone number in E164 format
            $value = $phoneUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);
        } //End if
        
        return $value;
    } //Function ends


    /**
     * Get the virtual phone number in E164 format
     */
    public function getVirtualPhoneNumberAttribute($value) {
        if (!empty($value)) {
            $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

            //Parse phone number
            $phoneNumberObject = $phoneUtil->parse($value, null);

            //Format phone number in E164 format
            $value = $phoneUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);
        } //End if

        return $value;
    } //Function ends


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.users');
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
     * Automatically creates verification token.
     *
     * @param  string  $value
     * @return void
     */
    public function setVerificationTokenAttribute($value)
    {
        $this->attributes['verification_token'] = Str::random(32);
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
