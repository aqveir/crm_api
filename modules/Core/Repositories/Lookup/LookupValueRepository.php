<?php

namespace Modules\Core\Repositories\Lookup;

use Config;

use App\Contracts\Lookup\{LookupValueContract};

use Modules\Core\Models\Lookup\LookupValue;
use Modules\Core\Repositories\EloquentRepository;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Class LookupValueRepository
 * 
 * @package Modules\Core\Repositories\Lookup
 */
class LookupValueRepository extends EloquentRepository
{

    /**
     * LookupValueRepository constructor.
     *
     * @param  LookupValue  $model
     */
    public function __construct(LookupValue $model)
    {
        $this->model = $model;
    }


    /**
	 * Get LookUpValue Object by Key
	 */
	public function getLookUpByKey(int $orgId, string $key)
	{
		$objReturnValue=null;
		
		try {
	        $query = $this->getAllLookUpData($orgId);
	        $query = $query->where('key', $key);
	        $query = $query->first();

	        $objReturnValue = $query;		
		} catch(Exception $e) {
			$objReturnValue=null;
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
	        $query = $this->getAllLookUpData($orgId);
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
     * Get All LookUp Information for an Organization from Cache
     *
     * @return object
     */
    public function getAllLookUpData(int $orgId) {
        $objReturnValue=null;
        try {
            //Get cache configuration
            $keyCache = config('core.settings.cache.lookup_value.key').'_'.(string)$orgId;
            $durationCache = config('core.settings.cache.lookup_value.duration_in_sec');

            if (Cache::has($keyCache)) {
                $objReturnValue = Cache::get($keyCache);
            } else {
                $objReturnValue = Cache::remember($keyCache, $durationCache/60, function() use ($orgId) {
                    return $this->getAllLookUpDataFromDB($orgId);
                });
            } //End if-else
        } catch(Exception $e) {
            $objReturnValue=null;
            Log::error(json_encode($e));
        } //Try-catch ends
        
        return $objReturnValue;
    } //Function ends
    

    /**
     * Get All LookUp Information for an Organization from DB
     *
     * @return object
     */
    private function getAllLookUpDataFromDB(int $orgId) {
        $objReturnValue=null;
        try {
            $query = $this->model;
            $query = $query->where('org_id', $orgId);
            $query = $query->orWhere('org_id', 0);
            $query = $query->orderBy('order', 'asc');
            $query = $query->orderBy('id', 'asc');
            $query = $query->get();

            $objReturnValue = $query;
        } catch(Exception $e) {
            $objReturnValue=null;
            Log::error(json_encode($e));
        } //Try-catch ends
        
        return $objReturnValue;
    } //Function ends

} //Class ends
