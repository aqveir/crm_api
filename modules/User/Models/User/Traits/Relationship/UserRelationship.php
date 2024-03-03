<?php

namespace Modules\User\Models\User\Traits\Relationship;

use Modules\User\Models\User\UserAvailability;

/**
 * Class User Relationship
 */
trait UserRelationship
{
	
	/**
	 * Show Organization
	 */
	public function organization()
	{
		return $this->belongsTo(
			config('aqveir-class.class_model.organization'), 
			'org_id', 'id'
		);
	} //Function ends


	/**
	 * Country
	 */
	public function country()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.country'), 
			'phone_idd_code', 'phone'
		);
	} //Function ends


	/**
	 * TimeZone
	 */
	public function timezone()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.timezone'), 
			'id', 'timezone_id'
		);
	} //Function ends


	/**
	 * Show Roles
	 */
	public function roles(int $orgId=0)
	{
		$orgId = $this->org_id??$orgId;
		return $this->belongsToMany(
			config('aqveir-class.class_model.role'),
			config('aqveir-migration.table_name.user.roles'),
			'user_id', 'role_id'
		)
		->withPivot('account_id')
		->wherePivot('org_id', $orgId);
	} //Function ends


	/**
	 * Show Active Roles
	 */
	public function active_roles(int $orgId=0)
	{
		return $this->roles($orgId)
			->wherePivot('is_active', 1);
	} //Function ends


	/**
	 * Show Privileges
	 */
	public function privileges(int $orgId=0)
	{
		$orgId = $this->org_id??$orgId;

		return $this->belongsToMany(
			config('aqveir-class.class_model.privilege'),
			config('aqveir-migration.table_name.user.privileges'),
			'user_id', 'privilege_id'
		)
		->wherePivot('org_id', $orgId);
	} //Function ends

	
	/**
	 * Show Granted Privileges
	 */
	public function active_privileges(int $orgId=0)
	{
		return $this->privileges($orgId)
			->wherePivot('is_active', 1);
	} //Function ends


	/**
	* Show user availablity status
	*/
	public function availability()
	{
		try {
			return $this->hasOne(
				UserAvailability::class,
				'user_id','id'
			)
			->whereNotNull('user_id');
		} catch(Exception $e) {
			return null;
		} //Try-catch ends
	} //Function ends


	/**
	* Show User Reportees
	*/
	public function user_reportess()
	{
		return $this->hasMany(
			config('aqveir-class.class_model.user_reportees'),
			'reportee_id','id'
		)
		->whereNotNull('reportee_id');
	} //Function ends

} //Trait ends
