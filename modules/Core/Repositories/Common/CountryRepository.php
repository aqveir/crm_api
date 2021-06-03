<?php

namespace Modules\Core\Repositories\Common;

use Config;
use Modules\Core\Contracts\{CountryContract};

use Modules\Core\Models\Common\Country;
use Modules\Core\Repositories\EloquentRepository;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Class CountryRepository
 * 
 * @package Modules\Core\Repositories\Common
 */
class CountryRepository extends EloquentRepository implements CountryContract
{
    /**
     * Repository constructor.
     *
     * @param  Country  $model
     */
    public function __construct(Country $model)
    {
        $this->model = $model;
    }


    /**
	 * Get Country Object by Code
	 */
	public function getCountryByCode(string $code, string $column='alpha3_code')
	{
		$objReturnValue=null;
		
		try {
	        $query = $this->model;
	        $query = $query->where($column, $code);
	        $query = $query->first();

	        $objReturnValue = $query;		
		} catch(Exception $e) {
			$objReturnValue=null;
			log::error(json_encode($e));
		}
		
		return $objReturnValue;
    } //Function ends


    /**
	 * Get LookUpValue Object by Identifier
	 */
	public function getLookUpById(int $orgId, int $lookupId)
	{
		$objReturnValue=null;
		
		try {
	        $query = $this->getAllCountryData();
	        $query = $query->where('id', $lookupId);
	        $query = $query->first();
	        //Log::debug($query);

	        $objReturnValue = $query;		
		} catch(Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-catch ends
		
		return $objReturnValue;
	} //Function ends


    /**
     * Get All Country Data from Cache
     *
     * @return object
     */
    public function getAllCountryData(bool $isCached=true) {
        $objReturnValue=null;
        try {
            //Get cache configuration
            $keyCache = config('omnichannel.settings.cache.country.key');
            $durationCache = config('omnichannel.settings.cache.country.duration_in_sec');

            if ($isCached && Cache::has($keyCache)) {
                $objReturnValue = Cache::get($keyCache);
            } else {
                $objReturnValue = Cache::remember($keyCache, $durationCache/60, function() {
                    return $this->getAllCountryDataFromDB();
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
    private function getAllCountryDataFromDB() {
        $objReturnValue=null;
        try {
            $query = $this->model;
            $query = $query->with('currency');
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
