<?php

namespace Modules\Document\Models\Document\Traits\Relationship;

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
			config('crmomni-class.class_model.lookup_value'),
			'id', 'entity_type_id'
		);
	} //Function ends


	/**
	 * Show Owner
	 */
	public function owner()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.user'),
			'id', 'created_by'
		);
	} //Function ends

} //Trait ends