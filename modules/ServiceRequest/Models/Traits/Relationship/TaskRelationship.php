<?php

namespace Modules\ServiceRequest\Models\Traits\Relationship;

use Modules\ServiceRequest\Models\ActivityParticipant;

/**
 * Class Task Relationship
 */
trait TaskRelationship
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
	 * Show Owner/User/Creator
	 */
	public function owner()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.user.main'),
			'id', 'created_by'
		);
    } //Function ends


	/**
	 * Show Task Assignee
	 */
	public function assignee()
	{
		return $this->hasOne(
			ActivityParticipant::class, 
			'activity_id', 'id'
		);
    } //Function ends


    /**
     * Task Type (Task)
     */
	public function type()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'type_id'
		);
	} //Function ends

	
    /**
     * Task SubType (Call/SMS/Email/Other)
     */
	public function subtype()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'subtype_id'
		);
	} //Function ends


    /**
     * Task Priority
     */
	public function priority()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'priority_id'
		);
	} //Function ends


    /**
     * Task Status
     */
	public function status()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'status_id'
		);
	} //Function ends

} //Trait ends