<?php

namespace Modules\Customer\Models\Customer\Traits\Relationship;

/**
 * Class Customer Detail Relationship
 */
trait CustomerDetailRelationship
{
	public function type()
	{
		return $this->hasOne(
			config('omnicrm-class.class_model.lookup_value'), 
			'id', 'type_id'
		);
	}

	public function subtype()
	{
		return $this->hasOne(
			config('omnicrm-class.class_model.lookup_value'), 
			'id', 'subtype_id'
		);
	}

	public function country()
	{
		return $this->hasOne(
			config('omnicrm-class.class_model.country'), 
			'id', 'country_id'
		);
	}

	public function customer()
	{
		return $this->belongsTo(
			config('omnicrm-class.class_model.customer.main'),  
			'customer_id', 'id'
		);
	}
}
