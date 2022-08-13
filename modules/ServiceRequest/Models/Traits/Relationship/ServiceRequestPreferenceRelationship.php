<?php

namespace Modules\ServiceRequest\Models\Traits\Relationship;


/**
 * Class ServiceRequestPreference Relationship
 */
trait ServiceRequestPreferenceRelationship
{
    
	/**
	 * Show Channel
	 */
	public function channel()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'channel_type_id'
		);
    } //Function ends
    
} //Trait ends
