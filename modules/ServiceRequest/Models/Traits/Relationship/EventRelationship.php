<?php

namespace Modules\ServiceRequest\Models\Traits\Relationship;


/**
 * Class Event Relationship
 */
trait EventRelationship
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
     * Event Type (Event)
     */
	public function type()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.lookup_value'),
			'id', 'type_id'
		);
	} //Function ends


    /**
     * Event SubType (Meeting/Custom)
     */
	public function subtype()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.lookup_value'),
			'id', 'status_id'
		);
	} //Function ends


	/**
	 * Event Participant
	 */
	public function participants()
	{
		return $this->hasMany(
			ServiceRequestEventParticipant::class, 
			'event_id', 'id'
		);
	}

} //Trait ends