<?php

namespace Modules\Core\Models\Common\Traits\Relationship;

/**
 * Class TimeZone Relationship
 */
trait TimeZoneRelationship
{
	/**
	 * Country
	 */
	public function country()
	{
		return $this->belongsTo(
			config('omnicrm-class.class_model.country'),
			'country_id', 'id'
		);
	} //Function ends

} //Trait ends