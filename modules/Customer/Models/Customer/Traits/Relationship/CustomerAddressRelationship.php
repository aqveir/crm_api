<?php

namespace Modules\Customer\Models\Customer\Traits\Relationship;

use Modules\Customer\Models\Common\Apartment;
use Modules\Customer\Models\Common\Society;

/**
 * Class Customer Address Relationship
 */
trait CustomerAddressRelationship
{
	/**
	 * Type
	 */
	public function type()
	{
		return $this->hasOne(
			config('omnicrm-class.class_model.lookup_value'), 
			'id', 'type_id'
		);
	}


	/**
	 * State
	 */
	public function state()
	{
		return $this->belongsTo(
			config('omnicrm-class.class_model.customer.main'),  
			'state_id', 'id'
		);
	}


	/**
	 * Customer
	 */
	public function customer()
	{
		return $this->belongsTo(
			config('omnicrm-class.class_model.customer.main'),  
			'customer_id', 'id'
		);
	}


	/**
	 * Apartment
	 */
	public function apartment()
	{
		return $this->hasOne(
			Apartment::class,  
			'id', 'apartment_id'
		);
	}


	/**
	 * Society
	 */
	public function society()
	{
		return $this->hasOne(
			Society::class,  
			'id', 'society_id'
		);
	}
	
} //Classends
