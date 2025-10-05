<?php

namespace Modules\Contact\Models\Contact\Traits\Action;

use Config;
use Modules\Contact\Models\Contact\Company;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Action methods on Company
 */
trait CompanyAction
{
	/**
	 * Create/Save New Company
	 */
	public function saveCompany(String $name)
	{
		$objReturnValue=null;
		try {
			$company = Company::firstOrNew([
				'name' => $name,
				'description' => $name
			]);

            if(!$company->save()) {
                throw new HttpException(500);
            } //End if

            //Get the Newly Created recommendation
            $objReturnValue = $company;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-Catch ends
		return $objReturnValue;
	} //Function ends

} //End Class
