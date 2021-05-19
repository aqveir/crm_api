<?php

namespace Modules\Contact\Models\Contact\Traits\Relationship;

/**
 * Class Contact Detail Relationship
 */
trait ContactDetailRelationship
{
	public function type()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.lookup_value'), 
			'id', 'type_id'
		);
	}

	public function subtype()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.lookup_value'), 
			'id', 'subtype_id'
		);
	}

	public function country()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.country'), 
			'id', 'phone_idd'
		);
	}

	public function contact()
	{
		return $this->belongsTo(
			config('crmomni-class.class_model.contact.main'),  
			'contact_id', 'id'
		);
	}
}
