<?php 

namespace Modules\Core\Models\Common;

use Modules\Core\Models\BaseModel as Model;
use Modules\Core\Models\Common\Traits\Relationship\CurrencyRelationship;

/**
 * Eloquent Model for the Currency
 */
class Currency extends Model {

    use CurrencyRelationship;


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
        'iso_code', 'iso_digit', 'display_value',
        'symbol', 'is_symbol_left_pos', 'decimal_places',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'iso_digit', 'is_active',
        'created_by', 'updated_by',
        'created_at', 'updated_at',
    ];


    /**
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'last_updated_at'
    ];


    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['symbol_uc', 'symbol_hex', 'last_updated_at'];


    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [];


    /**
     * Default constructor
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('aqveir-migration.table_name.currencies');
    }


    /**
     * Assessors for Currency Unicode Symbol
     */
    public function getSymbolUcAttribute()
    {
        $symbol = $this->attributes['symbol'];
        return (!empty($symbol)?('U+0' . $symbol):null);
    } //Function ends


    /**
     * Assessors for Currency Hex Symbol
     */
    public function getSymbolHexAttribute()
    {
        $symbol = $this->attributes['symbol'];
        return (!empty($symbol)?('&#x' . ltrim($symbol, '0')):null);
    } //Function ends

} //Class ends