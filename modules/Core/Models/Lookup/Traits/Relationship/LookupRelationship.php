<?php

namespace Modules\Core\Models\Lookup\Traits\Relationship;

/**
 * Class Lookup Relationship
 */
trait LookupRelationship
{
	public function values()
	{
		return $this->hasMany(
			config('crmomni-class.class_model.lookup_value'),
			'lookup_id', 'id'
		);
	}
}