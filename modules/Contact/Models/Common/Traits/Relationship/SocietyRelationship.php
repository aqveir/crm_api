<?php

namespace Modules\Contact\Models\Common\Traits\Relationship;

use Modules\Contact\Models\Common\Apartment;

/**
 * Class Society Relationship
 */
trait SocietyRelationship
{
	/**
	 * Apartments
	 */
	public function apartments()
	{
		return $this->hasMany(
			Apartment::class, 
			'society_id', 'id'
		);
    }
} //Trait ends