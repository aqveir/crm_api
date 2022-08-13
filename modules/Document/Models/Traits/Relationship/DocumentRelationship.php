<?php

namespace Modules\Document\Models\Traits\Relationship;

/**
 * Trait Document Relationship
 */
trait DocumentRelationship
{
	/**
	 * Show Type
	 */
	public function type()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'entity_type_id'
		);
	} //Function ends


	/**
	 * Show Owner
	 */
	public function owner()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.user.main'),
			'id', 'created_by'
		);
	} //Function ends

} //Trait ends
