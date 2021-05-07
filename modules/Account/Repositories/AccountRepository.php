<?php

namespace Modules\Account\Repositories;

use Modules\Account\Contracts\{AccountContract};

use Modules\Account\Models\Account;
use Modules\Core\Repositories\EloquentRepository;

use Illuminate\Support\Facades\Log;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AccountRepository
 * 
 * @package Module\Account\Repositories
 */
class AccountRepository extends EloquentRepository implements AccountContract
{

    /**
     * Repository constructor.
     *
     * @param \Account  $model
     */
    public function __construct(Account $model)
    {
        $this->model = $model;
    }


    /**
	 * Get Default Account for Organization
	 */
	public function getDefault(int $orgId)
	{
		$objReturnValue=null;
		
		try {
            $model = $this->model
                ->where('org_id', $orgId)
                ->where('is_default', true)
                ->first();

            //Check if the data exists
            if (empty($model)) {
                throw new NotFoundHttpException();
            } //End if

	        $objReturnValue = $model;
		} catch(NotFoundHttpException $e) {
            log::error('AccountRepository:getDefault:NotFoundHttpException:' . $e->getMessage());
			$objReturnValue=null;
		} catch(Exception $e) {
            log::error('AccountRepository:getDefault:Exception:' . $e->getMessage());
			$objReturnValue=null;
		} //Try-catch ends
		
		return $objReturnValue;
    } //Function ends


    /**
	 * Get Default Account for Organization
	 */
	public function setDefault(int $orgId, int $accountId)
	{
		$objReturnValue=null;
		
		try {
            $accounts = $this->model
                ->where('org_id', $orgId)
                ->where('is_default', true)
                ->get();

            //Set existing account to default=false
            if (!empty($accounts)) {
                foreach ($accounts as $account) {
                    if ($account['id'] != $accountId) {
                        $this->update($account['id'], 'id', ['is_default' => false]);
                    } //End if
                } //Loop ends
            } //End if

            //Get account to change
            $model = $this->model
                ->where('org_id', $orgId)
                ->where('id', $accountId)
                ->first();

            //Check if the data exists
            if (empty($model)) {
                throw new NotFoundHttpException();
            } //End if

            //Set default value
            $model['is_default'] = true;
            $model->save();

	        $objReturnValue = $model;
		} catch(NotFoundHttpException $e) {
            log::error('AccountRepository:getDefault:NotFoundHttpException:' . $e->getMessage());
			$objReturnValue=null;
		} catch(Exception $e) {
            log::error('AccountRepository:getDefault:Exception:' . $e->getMessage());
			$objReturnValue=null;
		} //Try-catch ends
		
		return $objReturnValue;
    } //Function ends
	
} //Class ends