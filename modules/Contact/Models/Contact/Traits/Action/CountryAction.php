<?php

namespace Modules\Contact\Models\Contact\Traits\Action;

use Config;
use Modules\Contact\Models\Contact\country;
use Illuminate\Support\Facades\Log;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Action methods on Country
 */
trait CountryAction
{
	/**
	 * Create/Save New Country
	 */
	  public function updateOrCreateCountryCode($countryCode)
    {
    	$objReturnValue = null;
    	try{
    		$country = config('aqveir-class.class_model.country')::firstOrCreate([
    			'code' => $countryCode
    		]);	

    		$objReturnValue = $country;
    	} catch(Exception $e) {
    		Log::error(json_encode($e));
            $objReturnValue = null;
    	} //Try-Catch ends
        
    	return $objReturnValue;
	} //Function ends

}
