<?php

namespace Modules\Preference\Models\Preference\Traits\Relationship;

use Modules\Preference\Models\Preference\Preference;
use Modules\Preference\Models\Preference\PreferenceDataValue;

/**
 * Class Preference Data Relationship
 */
trait PreferenceDataRelationship
{
	/**
	 * Prefernce Filter LookUp Values
	 */
	public function values()
	{
		return $this->hasMany(
			PreferenceDataValue::class,
			'data_id', 'id'
		);
	} //Function ends


	/**
	 * Preference Reference
	 */
	public function preference()
	{
		return $this->hasOne(
			Preference::class,
			'id', 'data_id'
		);
	} //Function ends

	// /**
	//  * Show Property Filter LookUp Values
	//  */
	// public function lookups()
	// {
	// 	return $this->hasMany(
	// 		config('portiqo-crm.class_model.propertyfilter_lookup'),
	// 		'id', 'filter_lookup_id'
	// 	);
	// } //Function ends

	// /**
	//  * Show Property Filter LookUp Values
	//  */
	// public function lookup_values()
	// {
	// 	return $this->hasMany(
	// 		config('portiqo-crm.class_model.propertyfilter_lookup_value'),
	// 		'filter_lookup_id', 'lookup_id'
	// 	);
	// } //Function ends
} //Trait ends
