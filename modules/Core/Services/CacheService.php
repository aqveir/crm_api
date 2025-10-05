<?php

namespace Modules\Core\Services;

use Config;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class CacheService
 * @package Modules\Core\Services
 */
class CacheService
{
    /**
     * Cache Key and Duration Settings
     */
    protected $cacheKey;
    protected $cacheDurationInSecs;


    /**
     * Constructor
     * 
     * 
     */
    public function __construct(string $key, int $duration=0) {
        $this->cacheKey = $key;
        $this->cacheDurationInSecs = $duration;
    } //Function ends


    /**
     * Get Cache Data based on the key
     * 
     * @param string    $key (optional)
     */
    public function get(string $key='')
    {
        $objReturnValue = null;
        try {
            //Get cache configuration
            $keyCache = $this->cacheKey.$key;

            //Cache::flush();
            if (Cache::has($keyCache)) {
                $objReturnValue = Cache::get($keyCache);
            } //End if
        } catch (Exception $e) {

        } //Try-Catch ends
        return $objReturnValue;
    } //Function ends


    /**
     * Set Cache Data based on the key
     * 
     * @param string    $key (optional)
     * @param any       $data
     */
    public function set(string $key='', $data)
    {
        $objReturnValue = null;
        try {
            //Get cache configuration
            $keyCache = $this->cacheKey.$key;

            //Clear existing cache
            $this->clear($key);

            $objReturnValue = Cache::rememberForever($keyCache, function() use ($data) {
                return $data;
            });
        } catch (Exception $e) {

        } //Try-Catch ends
        return $objReturnValue;
    } //Function ends


    /**
     * Clear Cache Data based on the key
     * 
     * @param string    $key (optional)
     */
    public function clear(string $key='') 
    {
        $objReturnValue = false;
        try {
            //Get cache configuration
            $keyCache = $this->cacheKey.$key;

            //Cache::flush();
            if (Cache::has($keyCache)) {
                Cache::forget('key');
            } //End if

            $objReturnValue = true;
        } catch (Exception $e) {
            
            throw new Exception();
        } //Try-Catch ends
        return $objReturnValue;
    } //Function ends

} //Class ends