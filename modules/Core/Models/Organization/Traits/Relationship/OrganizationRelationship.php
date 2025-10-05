<?php

namespace Modules\Core\Models\Organization\Traits\Relationship;

use Modules\Core\Models\Common\Configuration;

/**
 * Class Organization Relationship
 */
trait OrganizationRelationship
{

	/**
	 * Industry
	 */
	public function industry()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'industry_id'
		);
	} //Function ends


	/**
	 * Timezone
	 */
	public function timezone()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'timezone_id'
		);
	} //Function ends


	/**
	 * State
	 */
	public function state()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'state_id'
		);
	} //Function ends


	/**
	 * Country
	 */
	public function country()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'country_id'
		);
	} //Function ends


	/**
	 * Users
	 */
	public function users()
	{
		return $this->hasMany(
			config('aqveir-class.class_model.user.main'),
			'org_id', 'id'
		);
	} //Function End


	/**
	 * Configurations
	 */
	public function configurations()
	{
		return $this->belongsToMany(
			Configuration::class,
			config('aqveir-migration.table_name.organization_configurations'),
			'org_id', 'configuration_id'
		)
		->withPivot('value');
	} //Function End


	/**
	 * All Roles in Organization
	 */
	public function roles()
	{
		return $this->hasMany(
			config('aqveir-class.class_model.role'),
			'org_id', 'id'
		);
	} //Function End


	/**
	 * All Accounts in Organization
	 */
	public function accounts()
	{
		if (class_exists(config('aqveir-class.class_model.account'))) {
			return $this->hasMany(
				config('aqveir-class.class_model.account'),
				'org_id', 'id'
			);
		} else {
			return [];
		} //End if
	} //Function End

} //Trait Ends
