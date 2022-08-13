<?php

namespace Modules\Account\Models\Traits\Relationship;

/**
 * Trait Relationship
 */
trait AccountRelationship
{
    /**
	 * Show Type
	 */
	public function type()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'type_id'
		);
	} //Function ends

	
	/**
	 * Show Owner
	 */
	public function owner()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.user.main'),
			'id', 'owner_id'
		);
    } //Function ends
    

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

} //Trait ends