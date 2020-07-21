<?php

namespace Modules\User\Models\User\Traits\Action;

use Config;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Modules\Core\Models\User\User;
use Modules\Core\Models\User\UserReportees;
use Modules\Core\Models\User\UserRole;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Action methods on User
 */
trait UserAction
{

	/**
	 * Authenticate user based on the given
	 * conditions.
	 */
    public function authenticateUser($condition) {
        try {
            if(Auth::attempt($condition))
            {
                return Auth::user();
            } else {
                throw new HttpException(400);
            } //End if
        } catch (Exception $e) {
			throw new HttpException(400);
        } //Try-Catch ends
    } //Method ends

	/**
	 * Save user to the table
	 */
	public function saveUser(int $orgId, int $userId, $request) 
	{
		$objReturnValue=null;
		try {
	        $query = new User($request->all());
	        $query->org_id=$orgId;
	        $query->created_by=$userId;
	        $query->modified_by=$userId;

            if(!$query->save()) {
                throw new HttpException(500);
            } //End if

 			$objReturnValue = $query;   
		} catch (Exception $e) {
			Log::error(json_encode($e));
			$objReturnValue=null;
		} //Try-Catch ends

		return $objReturnValue;
	} //Function Ends


	/**
	 * Get User by Hash
	 */
	public function getUserByHash(int $orgId=0, String $hash)
	{
		$objReturnValue=null;
		try {
			$query = config('omnicrm-class.class_model.user')::where('hash', $hash);
			if($orgId>0) { $query = $query->where('org_id', $orgId); } //End if
			$query = $query->orderBy('id', 'asc')->firstOrFail();

            //Get the Contact Object
            $objReturnValue = $query;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-Catch ends
		return $objReturnValue;
	} //Function ends


	/**
	 * Get User by Identifier
	 */
	public function getUserById(int $orgId=0, int $id)
	{
		$objReturnValue=null;
		try {
			$query = config('omnicrm-class.class_model.user')::where('id', $id);
			if($orgId>0) { $query = $query->where('org_id', $orgId); } //End if
			$query = $query->orderBy('id', 'asc')->firstOrFail();

            //Get the Contact Object
            $objReturnValue = $query;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-Catch ends
		return $objReturnValue;
	} //Function ends


	/**
	 * Get User by Phone Number
     */
	public function getUserByPhoneNumber($phone)
	{
		$objReturnValue=null;
		try {  
			$query = config('omnicrm-class.class_model.user')::where('phone','like', '%'.$phone);
			$query = $query->where('is_active', 1)->firstOrFail();

			$objReturnValue = $query;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //End Try-Catch 
		return $objReturnValue;
	} //Function ends


    /**
     * Get User By Virtual Number
     */
	public function getUserByVirtualNumber($virtualNumber)
	{
		$objReturnValue=null;
		try {  
			$query = config('omnicrm-class.class_model.user')::where('virtual_phone_number','like', '%'.$virtualNumber);
			$query = $query->where('is_active', 1)->firstOrFail();

			$objReturnValue = $query;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //End Try-Catch
		return $objReturnValue;
	} //Function ends


	/**
     * Get User By Reports To
     */
	public function getReportsToUser(int $orgId, int $userId)
	{
		$objReturnValue=null;
		try {
			$query = config('omnicrm-class.class_model.user')::where('org_id', $orgId);
			$query = $query->where('reports_to', $userId);
			$query = $query->get();

			$objReturnValue=$query;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //End Try-Catch

		return $objReturnValue;
	} //Function ends


	/**
     * Get User Role
     */
	public function getUserRole(int $orgId, int $userId)
	{
		$objReturnValue=null;
		try {
			$query = config('omnicrm-class.class_model.role')::with(['users']);
			$query = $query->whereHas('users', function ($inner_query) use ($userId) { 
                $inner_query->where('user_id', $userId);
            });

			$query = $query->where('org_id', $orgId);
			$query = $query->firstOrFail();

			$objReturnValue=$query;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //End Try-Catch

		return $objReturnValue;
	} //Function ends


	/**
     * Save User Reportees
     */
	public function saveUserReportees(int $userId, $request)
	{	
		$objReturnValue=null;
		try {
			$query = new UserReportees($request);
			$query->created_by = $userId;
			$query->modified_by = $userId;

            if(!$query->save()) {
                throw new HttpException(500);
            } //End if

            $objReturnValue=$query;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //End Try-Catch

		return $objReturnValue;
	} //Function ends


	/**
     * Save Or Create New User Role
     */
    public function saveOrUpdateUserRole($request) 
    {
        $objReturnValue=null;
        try {
            //Get request parameters and save
            $query = new UserRole();
            $query = $query->updateOrCreate($request);

            if(!$query->save()) {
                throw new HttpException(500);
            } //End if

            $objReturnValue=$query;
        } catch (Exception $e) {
            Log::error(json_encode($e));
            $objReturnValue=null; 
        } //Try-catch ends

        return $objReturnValue;
    } //End Function


    /**
     * Update User Data
     */
    public function updateUser(int $orgId, string $hash, int $userId, $request)
    {	
    	$objReturnValue=false;
    	try {
    		$query = config('omnicrm-class.class_model.user')::where('org_id', $orgId);
    		$query = $query->where('hash', $hash);
    		$query = $query->update([
	    				'first_name' => $request['first_name'], 
	    				'last_name' => $request['last_name'], 
	    				'middle_name' => $request['middle_name'],
	    				'email' => $request['email'], 
	    				'phone'=>$request['phone'],
	    				'virtual_phone_number'=> $request['virtual_phone_number'],
	    				'modified_by' => $userId,
	    				'modified_on'=> Carbon::now()
    				]);
   
    		$objReturnValue=true;
    	} catch (Exception $e) {
            Log::error(json_encode($e));
            $objReturnValue=false; 
    	} //Try-catch ends

    	return $objReturnValue;
    } //Function ends
} //Trait ends
