<?php

namespace Modules\Core\Models\Common\Traits\Action;

/**
 * Class Configuration Action
 */
trait ConfigurationAction
{
	/**
	 * Get Configuration Key
	 */
	public function getByKey(string $key)
	{
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

} //Trait ends