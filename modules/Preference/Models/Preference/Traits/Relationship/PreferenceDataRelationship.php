<?php

namespace Modules\Preference\Models\Preference\Traits\Relationship;

/**
 * Class Preference Data Relationship
 */
trait PreferenceDataRelationship
{
	/**
	 * Show Property Filter LookUp Values
	 */
	public function values()
	{
		return $this->hasMany(
			config('portiqo-crm.class_model.propertyfilter_lookup_value'),
			'filter_lookup_id', 'id'
		);
	} //Function ends

	/**
	 * Show Property Filter LookUp Values
	 */
	public function lookups()
	{
		return $this->hasMany(
			config('portiqo-crm.class_model.propertyfilter_lookup'),
			'id', 'filter_lookup_id'
		);
	} //Function ends

	/**
	 * Show Property Filter LookUp Values
	 */
	public function lookup_values()
	{
		return $this->hasMany(
			config('portiqo-crm.class_model.propertyfilter_lookup_value'),
			'filter_lookup_id', 'lookup_id'
		);
	} //Function ends
} //Trait ends
