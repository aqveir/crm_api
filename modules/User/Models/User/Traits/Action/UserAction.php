<?php

namespace Modules\User\Models\User\Traits\Action;

use Config;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Action methods on User
 * 
 * @return \bool objReturnValue
 */
trait UserAction
{

	/**
	 * Check if the User has the given roles
	 */
    public function hasRoles(Array $roles, bool $isStrict=false) {
		$returnValue = false;
        try {
            if ($roles) {
                //Check Roles with active privileges
                $user_roles = collect($this->roles()->get());

                //Find in collection
                $exists = false;
                foreach ($roles as $key=>$roleName) {
                    $boolData = $user_roles->contains('key', $roleName);
                    $exists = (($key>0)?$exists:true) && $boolData;
                    $returnValue = ($isStrict)?($exists):($returnValue || $boolData);                  
                } //Loop ends
            } //End if
        } catch (Exception $e) {
			$returnValue = false;
		} //Try-Catch ends
		
		return $returnValue;
	} //Function End


    /**
     * Function to query Get User All Privileges 
     *
     * @return \bool objReturnValue
     */
    public function getActivePrivileges()
    {
        $objReturnValue=null; $userPrivileges=[];
        try { 
            //Check Roles with active privileges
            $userRoles = $this->roles()->get();
            if(!empty($userRoles)) {
                //Iterate the roles assigned
                foreach($userRoles as $userRole) {
                    //Get privileges for the role
                    $privileges = $userRole->active_privileges()->get();

                    //Iterate the privileges in each role
                    foreach ($privileges as $privilege) {

                        //Duplicate check to add the privileges
                        if (!in_array($privilege, $userPrivileges, TRUE)) {
                            array_push($userPrivileges, $privilege);
                        } //End if
                    } //Loop ends (privileges)
                } //Loop ends (userRoles)
            } //End if
            
            //Check for extra granted privileges
            $extraPrivileges = $this->grant_privileges()->get();
            if (!empty($extraPrivileges)) {
                foreach($extraPrivileges as $privilege) {
                    //Duplicate check to add the privileges
                    if (!in_array($privilege, $userPrivileges)) {
                        array_push($userPrivileges, $privilege);
                    } //End if
                } //Loop ends (privileges)
            } //End if
                
            $objReturnValue = $userPrivileges;
        } catch (Exception $e) {
            $objReturnValue=null;
            log::error(json_encode($e));
        } //Try-catch ends

        return $objReturnValue;
    } //Function End

} //Trait ends
