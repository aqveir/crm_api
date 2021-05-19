<?php

namespace Modules\Core\Repositories\Organization;

use Config;

use Modules\Core\Contracts\{OrganizationContract};

use Modules\Core\Models\Organization\Organization;
use Modules\Core\Repositories\EloquentRepository;

use Modules\Core\Transformers\Organization\OrganizationResource;
use Modules\Core\Transformers\Organization\OrganizationMiniResource;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class OrganizationRepository
 * 
 * @package Modules\Core\Repositories\Organization
 */
class OrganizationRepository extends EloquentRepository implements OrganizationContract
{

    /**
     * Repository constructor.
     *
     * @param  Organization  $model
     */
    public function __construct(Organization $model)
    {
        $this->model = $model;
    }


    /**
	 * Get Organization Object by Hash Identifier
	 */
	public function getOrganizationByHash(string $hash, bool $isActive=null)
	{
		$objReturnValue=null;
		
		try {
            $query = $this->model;
            $query = $query->where('hash', $hash);
            $query = $query->withCount('users');
            $query = $query->first();

            //lazy load relations
            if (empty($query)) {
                throw new ModelNotFoundException();
            } //End if
            $query = $query->load(['users', 'configurations']);

            $objReturnValue = $query;

            //Transform data
            //$objReturnValue = new OrganizationResource($query);	
		} catch(Exception $e) {
			$objReturnValue=null;
			throw $e;
		} //Try-catch ends
		
		return $objReturnValue;
    } //Function ends
    

    /**
     * Clear Organization Cache
     */
    public function setOrganizationClearCache()
    {
        $objReturnValue=null;
        try {
            //Get cache configuration
            $keyCache = config('core.settings.cache.organization.key');

            //Clear the cache
            if (Cache::has($keyCache)) {
                Cache::forget($keyCache);
                $objReturnValue=true;
            } //End if-else
        } catch(Exception $e) {
            $objReturnValue=null;
            throw $e;
        } //Try-catch ends
        
        return $objReturnValue;
    } //Function ends


    /**
     * Get All Organization Information from Cache
     *
     * @return object
     */
    public function getAllOrganizationsData() {
        $objReturnValue=null;
        try {
            //Get cache configuration
            $keyCache = config('core.settings.cache.organization.key');
            $durationCache = config('core.settings.cache.organization.duration_in_sec');

            if (Cache::has($keyCache)) {
                $objReturnValue = Cache::get($keyCache);
            } else {
                $objReturnValue = Cache::remember($keyCache, $durationCache/60, function() {
                    return $this->getAllOrganizationsFromDB();
                });
            } //End if-else
        } catch(Exception $e) {
            $objReturnValue=null;
            throw $e;
        } //Try-catch ends
        
        return $objReturnValue;
    } //Function ends
  

    /**
     * Get All Organization Information from DB
     *
     * @return object
     */
    private function getAllOrganizationsFromDB() {
        $objReturnValue=null;
        try {
            $query = $this->model;
            $query = $query->withCount('users');
            $query = $query->orderBy('id', 'asc');
            $query = $query->get();

            //Transform data
            $objReturnValue = OrganizationMiniResource::collection($query);
        } catch(Exception $e) {
            $objReturnValue=null;
            Log::error(json_encode($e));
        } //Try-catch ends
        
        return $objReturnValue;
    } //Function ends

} //Class ends