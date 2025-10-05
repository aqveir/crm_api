<?php

namespace Modules\Core\Repositories\Common;

use Config;
use Modules\Core\Contracts\{CurrencyContract};

use Modules\Core\Models\Common\Currency;
use Modules\Core\Repositories\EloquentRepository;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Class CurrencyRepository
 * 
 * @package Modules\Core\Repositories\Common
 */
class CurrencyRepository extends EloquentRepository implements CurrencyContract
{
    /**
     * Repository constructor.
     *
     * @param  Currency  $model
     */
    public function __construct(Currency $model)
    {
        $this->model = $model;
    }


    /**
	 * Get Currency Object by Code
	 */
	public function getCurrencyByCode(string $code)
	{
		$objReturnValue=null;
		
		try {
	        $query = $this->model;
	        $query = $query->where('iso_code', $code);
	        $query = $query->first();

	        $objReturnValue = $query;		
		} catch(Exception $e) {
			$objReturnValue=null;
			log::error(json_encode($e));
		}
		
		return $objReturnValue;
    } //Function ends


    /**
     * Get All Currency Data from Cache
     *
     * @return object
     */
    public function getAllCurrencyData(bool $isActive=true, bool $isCached=true) {
        $objReturnValue=null;
        try {
            //Get cache configuration
            $keyCache = config('omnichannel.settings.cache.currency.key');
            $durationCache = config('omnichannel.settings.cache.currency.duration_in_sec');

            if ($isCached && Cache::has($keyCache)) {
                $objReturnValue = Cache::get($keyCache);
            } else {
                $objReturnValue = Cache::remember($keyCache, $durationCache/60, function() use ($isActive) {
                    return $this->getAllCurrencyDataFromDB($isActive);
                });
            } //End if-else
        } catch(Exception $e) {
            $objReturnValue=null;
            Log::error(json_encode($e));
        } //Try-catch ends
        
        return $objReturnValue;
    } //Function ends


    /**
     * Get All Country Data from DB
     *
     * @return object
     */
    private function getAllCurrencyDataFromDB(bool $isActive) {
        $objReturnValue=null;
        try {
            $query = $this->model;
            $query = $query->with('countries');
            $query = $query->orderBy('id', 'asc');
            $query = $query->get();

            $objReturnValue = $query;
        } catch(Exception $e) {
            $objReturnValue=null;
            Log::error($e);
        } //Try-catch ends
        
        return $objReturnValue;
    } //Function ends

} //Class ends
