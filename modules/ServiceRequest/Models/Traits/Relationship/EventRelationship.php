<?php

namespace Modules\ServiceRequest\Models\Traits\Relationship;

use Log;
use Modules\ServiceRequest\Models\ActivityParticipant;


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
     * Event Type (Event)
     */
	public function type()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'type_id'
		);
	} //Function ends


    /**
     * Event SubType (Meeting/Custom)
     */
	public function subtype()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'subtype_id'
		);
	} //Function ends


	/**
	 * Event Participants
	 */
	public function participants()
	{
		return $this->hasMany(
			ActivityParticipant::class, 
			'activity_id', 'id'
		);
	} //Function ends

} //Trait ends