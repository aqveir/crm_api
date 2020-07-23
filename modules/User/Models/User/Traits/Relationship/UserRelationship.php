<?php

namespace Modules\User\Models\User\Traits\Relationship;

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
			config('crmomni-class.class_model.organization'), 
			'org_id', 'id'
		);
	} //Function ends


	/**
	 * Country
	 */
	public function country()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.country'), 
			'id', 'country_id'
		);
	} //Function ends


	/**
	 * TimeZone
	 */
	public function timezone()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.timezone'), 
			'id', 'timezone_id'
		);
	} //Function ends


	/**
	 * Show Roles
	 */
	public function roles()
	{
		return $this->belongsToMany(
			config('crmomni-class.class_model.role'),
			config('crmomni-migration.table_name.user.roles'),
			'user_id', 'role_id'
		);
	} //Function ends


	/**
	 * Show Privileges
	 */
	public function privileges()
	{
		return $this->belongsToMany(
			config('crmomni-class.class_model.privilege'),
			config('crmomni-migration.table_name.user_privileges'),
			'user_id', 'privilege_id'
		);
	} //Function ends

	
	/**
	 * Show Granted Privileges
	 */
	public function grant_privileges()
	{
		return $this->belongsToMany(
			config('crmomni-class.class_model.privilege'),
			config('crmomni-migration.table_name.user.privileges'),
			'user_id', 'privilege_id'
		)
		->wherePivot('is_active', 1);
	} //Function ends


	/**
	* Show userAvailablity status
	*/
	public function availability()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.user_availability'),
			'user_id','id'
		)
		->whereNotNull('user_id');
	} //Function ends


	/**
	* Show userAvailablity status
	*/
	public function user_reportess()
	{
		return $this->hasMany(
			config('crmomni-class.class_model.user_reportees'),
			'reportee_id','id'
		)
		->whereNotNull('reportee_id');
	} //Function ends

} //Trait ends
