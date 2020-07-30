<?php

namespace Modules\Preference\Models\Preference\Traits\Relationship;

/**
 * Class Preference Relationship
 */
trait PreferenceRelationship
{
	public function type()
	{
		return $this->hasOne(
			config('portiqo-crm.class_model.lookup_value'),
			'id', 'type'
		);
	}

	public function lookup()
	{
		return $this->hasOne(
			config('portiqo-crm.class_model.propertyfilter_lookup'),
			'id', 'lookup_id'
		);
	}
}
