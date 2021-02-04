<?php

namespace Modules\Agency\Models\Traits\Relationship;

/**
 * Trait Relationship
 */
trait AgencyRelationship
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
    

	/**
	 * Show Organization
	 */
	public function organization()
	{
		return $this->belongsTo(
			config('crmomni-class.class_model.organization'), 
			'org_id', 'id'
		);
	} //Function ends

} //Trait ends