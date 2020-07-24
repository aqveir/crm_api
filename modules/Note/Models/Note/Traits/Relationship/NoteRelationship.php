<?php

namespace Modules\Note\Models\Note\Traits\Relationship;

/**
 * Trait Note Relationship
 */
trait NoteRelationship
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
			config('crmomni-class.class_model.user.main'),
			'id', 'created_by'
		);
	} //Function ends

} //Trait ends
