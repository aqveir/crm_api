<?php

namespace Modules\User\Models\User\Traits\Relationship;

/**
 * Class UserAvailability Relationship
 */
trait UserAvailabilityRelationship
{
	/**
	 * Show Status
	 */
	public function status()
	{
		return $this->hasOne(
			config('omnicrm-class.class_model.lookup_value'),
			'id', 'status_id'
		);
	} //Function ends

	/**
	 * Show User
	 */
	public function user()
	{
		return $this->belongsTo(
			config('omnicrm-class.class_model.user'),
			'user_id', 'id'
		);
	} //Function ends

} //Trait ends
