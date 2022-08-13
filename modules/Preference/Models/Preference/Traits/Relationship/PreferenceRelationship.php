<?php

namespace Modules\Preference\Models\Preference\Traits\Relationship;

use Modules\Preference\Models\Preference\PreferenceData;

/**
 * Class Preference Relationship
 */
trait PreferenceRelationship
{
	/**
	 * Preference Type
	 */
	public function type()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'type_id'
		);
	}


	/**
	 * Preference Lookup Values for JSON Data Type
	 */
	public function data()
	{
		return $this->belongsTo(
			PreferenceData::class,
			'data_id', 'id'
		);
	}

} //Traits ends
