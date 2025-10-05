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
			config('aqveir-class.class_model.lookup_value'), 
			'id', 'type_id'
		);
	}


	/**
	 * State
	 */
	public function state()
	{
		return $this->belongsTo(
			config('aqveir-class.class_model.contact.main'),  
			'state_id', 'id'
		);
	}


	/**
	 * Contact
	 */
	public function contact()
	{
		return $this->belongsTo(
			config('aqveir-class.class_model.contact.main'),  
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
	} //Function ends


	/**
	 * Notes for the Contact Address
	 */
	public function notes()
	{
		if (class_exists(config('aqveir-class.class_model.note'))) {
			return $this->hasMany(
				config('aqveir-class.class_model.note'),
				'reference_id', 'id'
			)
			->with(['type', 'owner'])
			->whereHas('type', function($inner_query){$inner_query->where('key', 'entity_type_contact_address');})
			->orderBy('created_at', 'desc');
		} else {
			return [];
		} //End if
	} //Function ends
	
} //Classends
