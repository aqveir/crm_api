<?php

namespace Modules\ServiceRequest\Models\Traits\Relationship;


/**
 * Class Communication Relationship
 */
trait CommunicationRelationship
{
    
    /**
	 * Organization
	 */
	public function organization()
	{
		return $this->belongsTo(
			config('aqveir-class.class_model.organization'),
			'org_id', 'id'
		);
	} //Function ends
    

    /**
	 * Service Request
	 */
	public function servicerequest()
	{
		return $this->belongsTo(
			config('aqveir-class.class_model.service_request.main'),
			'servicerequest_id', 'id'
		);
    } //Function ends


	/**
	 * Communication Type
	 */
	public function type()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'activity_subtype_id'
		);
    } //Function ends
	
	    
	/**
	 * Communication Direction
	 */
	public function direction()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'direction_id'
		);
	} //Function ends
	
} //Trait ends
