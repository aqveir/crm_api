<?php

namespace Modules\Core\Models;

use Config;
use Exception;
use Carbon\Carbon;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 * @package Modules\Core\Models
 */
abstract class BaseModel extends Model
{
    /**
     * Generate Random Hash value that is always unique
     * using the current time logic.
     * 
     * Format: o20200234xxxxxxxx4
     *         
     */
    public function generateRandomHash(string $prefix='', bool $includeYear=true) {
        $objReturnValue=null;
        try {
            $hash ='';
            $dtCurrent = Carbon::now();

            $hash .= $prefix;
            if ($includeYear) {
                $hash .= (string) $dtCurrent->format('Ym');
            } //End if
            $hash .= (string) md5($dtCurrent->valueOf());

            $objReturnValue = strtolower($hash);
        } catch(Exception $e) {
            Log::error('BaseService:generateRandomHash:Exception:' . $e->getMessage());
        } //Try-catch ends
        
        return $objReturnValue;
    } //Function ends


    /**
     * Get the computed date of last modification of event
     *
     * @return datetime
     */
    public function getLastUpdatedAtAttribute() {
        $objReturnValue=null;
        try {
            $updatedAt=null;
            if (empty($this->attributes['updated_at'])) {
                $updatedAt = Carbon::parse($this->attributes['created_at']);
            } else {
                $updatedAt = Carbon::parse($this->attributes['updated_at']);
            }

            if (!empty($updatedAt)) {
                $objReturnValue = $updatedAt->format(config('aqveir.settings.date_format_response_generic'));
            } //End if
        } catch(Exception $e) {

        }

        return $objReturnValue;
    } //Function ends


    /**
     * Generate Slug String
     * 
     * @return string
     */
    public function generateText2Slug(String $text) {
        $objReturnValue=null;
        try {
            $objReturnValue = Str::slug($text, '-');
        } catch(Exception $e) {
            Log::error('BaseService:generateSlug:Exception:' . $e->getMessage());
        } //Try-catch ends
        
        return $objReturnValue;
    } //Function ends


    /**
     * Scope a query to only include active users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    } //Function ends


    /**
     * Get the computed date of last modification of event
     *
     * @return datetime
     */
    public function getPhonesAtAttribute() {
        $objReturnValue=null;
        try {
            $phone=$this->attributes['phone'];
            if (!empty($phone)) {
                
            } //End if

            $objReturnValue = null;
        } catch(Exception $e) {

        } //Try-catch ends

        return $objReturnValue;
    } //Function ends
} //Class ends