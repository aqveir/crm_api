<?php

namespace Modules\Note\Models\Traits\Relationship;

/**
 * Trait Relationship
 */
trait NoteRelationship
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


	/**
	 * Show Organization
	 */
	public function organization()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.organization'),
			'id', 'org_id'
		);
	} //Function ends

} //Trait ends
