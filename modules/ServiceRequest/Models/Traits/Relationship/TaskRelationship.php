<?php

namespace Modules\ServiceRequest\Models\Traits\Relationship;


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
			config('crmomni-class.class_model.organization'),
			'org_id', 'id'
		);
    } //Function ends
    

    /**
	 * Service Request
	 */
	public function servicerequest()
	{
		return $this->belongsTo(
			config('crmomni-class.class_model.service_request.main'),
			'servicerequest_id', 'id'
		);
    } //Function ends
    

	/**
	 * Show Owner/User/Creator
	 */
	public function owner()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.user.main'),
			'id', 'created_by'
		);
    } //Function ends


	/**
	 * Show Task Assignee
	 */
	public function assignee()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.user.main'),
			'id', 'user_id'
		);
    } //Function ends


    /**
     * Task Type (Task)
     */
	public function type()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.lookup_value'),
			'id', 'type_id'
		);
	} //Function ends

	
    /**
     * Task SubType (Call/SMS/Email/Other)
     */
	public function subtype()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.lookup_value'),
			'id', 'status_id'
		);
	} //Function ends


    /**
     * Task Priority
     */
	public function priority()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.lookup_value'),
			'id', 'priority_id'
		);
	} //Function ends

} //Trait ends