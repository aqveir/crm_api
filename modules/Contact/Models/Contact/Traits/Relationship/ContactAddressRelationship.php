<?php

namespace Modules\Contact\Models\Contact\Traits\Relationship;

use Modules\Contact\Models\Common\Apartment;
use Modules\Contact\Models\Common\Society;

/**
 * Class Contact Address Relationship
 */
trait ContactAddressRelationship
{
	/**
	 * Type
	 */
	public function type()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.lookup_value'), 
			'id', 'type_id'
		);
	}


	/**
	 * State
	 */
	public function state()
	{
		return $this->belongsTo(
			config('crmomni-class.class_model.contact.main'),  
			'state_id', 'id'
		);
	}


	/**
	 * Contact
	 */
	public function contact()
	{
		return $this->belongsTo(
			config('crmomni-class.class_model.contact.main'),  
			'contact_id', 'id'
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
