<?php

namespace Modules\Contact\Models\Common\Traits\Relationship;

use Modules\Contact\Models\Common\Society;

/**
 * Class Apartment Relationship
 */
trait ApartmentRelationship
{
	/**
	 * Society
	 */
	public function society()
	{
		return $this->belongsTo(
			Society::class, 
			'society_id', 'id'
		);
    }
} //Trait ends