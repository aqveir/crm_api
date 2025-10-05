<?php 

namespace Modules\Contact\Models\Contact;

use Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Modules\Core\Models\BaseModel as Model;
use Modules\Contact\Models\Contact\Traits\Relationship\ContactRelationship;
use Modules\Contact\Models\Contact\Traits\Action\ContactAction;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Eloquent Contact Model
 */
class Contact extends Model implements
    JWTSubject,
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;
    use Notifiable, ContactRelationship, ContactAction;
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
        'org_id', 'avatar', 
        'first_name','middle_name','last_name',
        'type_id', 'gender_id', 'birth_at',
        'extras', 'settings', 'search_tags',
        'referred_by',
    ];


    /**
     * Protected attributes that CANNOT be mass assigned.
     *
     * @var array
     */
    protected $guarded = [ 
        'id', 'hash', 'remember_token', 'last_login_at', 
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'org_id', 'remember_token',
        'first_name','middle_name','last_name', 'avatar', 'birth_at',
        'type_id', 'gender_id',
        'provider', 'provider_id', 'last_login_at', 'referral_code', 'allow_login',
        'failed_attempts', 'max_failed_attempts', 'is_active', 'ip_address', 'is_verified',
        'created_by', 'updated_by', 'deleted_by', 'referred_by',
        'created_at', 'updated_at', 'deleted_at'
    ]; 


    /**
     * The attributes that are represented as dates
     * 
     * @var array
     */
    protected $dates = [
        'birth_at', 'last_login_at',
        'created_at', 'updated_at', 'deleted_at',
        'last_updated_at'
    ];


    /**
     * The accessors to append to the model's array form.
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
        'is_primary' => 'boolean', 
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
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
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.contact.main');
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
    } //Function ends


    /**
     * Name Initials of Contact
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
     * Name Full Name of Contact
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
        $this->attributes['hash'] = $this->generateRandomHash('c');
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


    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array|string
     */
    public function routeNotificationForMail($notification)
    {
        //Contact Full Name
        $full_name = $this->getFullNameAttribute();

        //Get Contact Details (Email - Active + Primary)
        $contactDetail = $this->getContactDetailsForEmail(true, true);

        return [$contactDetail['identifier'] => $full_name ];
    } //Function ends

} //Class ends