<?php

namespace Modules\ServiceRequest\Models\Traits\Relationship;

use Modules\ServiceRequest\Models\ServiceRequestSource;
use Modules\ServiceRequest\Models\Task;
use Modules\ServiceRequest\Models\Event as ServiceRequestEvent;

use App\Models\ServiceRequest\ServiceRequestPropFilters;
use App\Models\ServiceRequest\ServiceRequestRecommendations;

/**
 * Class ServiceRequest Relationship
 */
trait ServiceRequestRelationship
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
	 * Account
	 */
	public function account()
	{
		return $this->belongsTo(
			config('aqveir-class.class_model.account'),
			'account_id', 'id'
		);
	} //Function ends
	

	/**
	 * Show Contact
	 */
	public function contact()
	{
		return $this->belongsTo(
			config('aqveir-class.class_model.contact.main'),
			'contact_id', 'id'
		);
	} //Function ends


	/**
	 * Show Owner/User/Creator
	 */
	public function owner()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.user.main'),
			'id', 'owner_id'
		);
	} //Function ends


	/**
	 * Show Category (Lead/Opportunity/Support/Custom)
	 */
	public function category()
	{
		return $this->belongsTo(
			config('aqveir-class.class_model.lookup_value'),
			'category_id', 'id'
		);
	} //Function ends


	/**
	 * Show Status
	 */
	public function status()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'status_id'
		);
	} //Function ends


	/**
	 * Show Stage
	 */
	public function stage()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'stage_id'
		);
	} //Function ends


	/**
	 * Show Type (Default/Custom)
	 */
	public function type()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'type_id'
		);
	} //Function ends


	/**
	 * Show Sources
	 */
	public function sources()
	{
		return $this->belongsToMany(
			ServiceRequestSource::class,
			config('aqveir-migration.table_name.service_request.source-data'),
			'servicerequest_id', 'source_id'
		);
	} //Function ends


	/**
	 * Show Preferences
	 */
	public function preferences()
	{
		return $this->hasMany(
			ServiceRequestPropFilters::class,
			'sr_id', 'id'
		);
	} //Function ends


	/**
	 * Show Recommendation
	 */
	public function recommendations()
	{
		return $this->hasMany(
			ServiceRequestRecommendations::class,
			'sr_id', 'id'
		);
	} //Function ends


	/**
	 * Show Tasks
	 */
	public function tasks()
	{
		return $this->hasMany(
			Task::class,
			'servicerequest_id', 'id'
		)->with([
			'type'
		])->whereHas('type', function ($inner_query) { 
			$inner_query->where('key', config('servicerequest.settings.lookup_value.task_key'));
		});
	} //Function ends


	/**
	 * Show Events
	 */
	public function events()
	{
		return $this->hasMany(
			ServiceRequestEvent::class,
			'servicerequest_id', 'id'
		)->with([
			'type'
		])->whereHas('type', function ($inner_query) { 
			$inner_query->where('key', config('servicerequest.settings.lookup_value.event_key'));
		});
	} //Function ends


	/**
	 * Show Event Communications
	 */
	public function communications()
	{
		return $this->hasMany(
			ServiceRequestEvent::class,
			'sr_id', 'id'
		)->with([
			'type','subtype'
		])->whereHas('type', function ($inner_query) { 
			$inner_query->whereIn('value', config('portiqo-crm.settings.lookup_value.service_request_event_communication'));
		})->whereHas('subtype', function ($inner_query) { 
			$inner_query->whereIn('value', config('portiqo-crm.settings.lookup_value.service_request_communication_modes'));
		});
	} //Function ends


	/**
	 * Show Notes
	 */
	public function notes()
	{
        return $this->hasMany(
            config('aqveir-class.class_model.note'),
            'reference_id', 'id'
        )
        ->with(['type'])
        ->whereHas('type', function($inner_query){ $inner_query->where('key', '=', 'entity_type_service_request');} )
        ->orderBy('created_at', 'desc');
	} //Function ends
 

	/**
	 * Documents for the Service Request
	 */
	public function documents()
	{
		return $this->hasMany(
			config('aqveir-class.class_model.document'),
			'reference_id', 'id'
        )
        ->with(['type'])
        ->whereHas('type', function($inner_query){$inner_query->where('key', '=', 'entity_type_servicerequest');})
        ->where('is_active', 1)
		->orderBy('created_at', 'desc');
	} //Function ends

} //Trait ends
