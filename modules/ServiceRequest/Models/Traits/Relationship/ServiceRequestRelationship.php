<?php

namespace Modules\ServiceRequest\Models\Traits\Relationship;

use App\Models\ServiceRequest\PropertySource;
use App\Models\ServiceRequest\ServiceRequestPropFilters;
use App\Models\ServiceRequest\ServiceRequestEvent;
use App\Models\ServiceRequest\ServiceRequestRecommendations;

/**
 * Class Service Request Relationship
 */
trait ServiceRequestRelationship
{
	/**
	 * Show Status
	 */
	public function status()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.lookup_value'),
			'id', 'status_id'
		);
	} //Function ends


	/**
	 * Show Stage
	 */
	public function stage()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.lookup_value'),
			'id', 'stage_id'
		);
	} //Function ends


	/**
	 * Show Type
	 */
	public function type()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.lookup_value'),
			'id', 'type_id'
		);
	} //Function ends


	/**
	 * Show Owner/User/Creator
	 */
	public function owner()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.user.main'),
			'id', 'owner_id'
		);
	} //Function ends


	/**
	 * Show Contact
	 */
	public function contact()
	{
		return $this->hasOne(
			config('crmomni-class.class_model.contact.main'),
			'id', 'contact_id'
		);
	} //Function ends


	/**
	 * Show Sources
	 */
	public function sources()
	{
		return $this->belongsToMany(
			PropertySource::class,
			config('portiqo-crm.table_name.servicerequest_sources'),
			'sr_id', 'property_source_id'
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
			ServiceRequestEvent::class,
			'sr_id', 'id'
		)->with([
			'type','subtype'
		])->whereHas('type', function ($inner_query) { 
			$inner_query->where('value', config('portiqo-crm.settings.lookup_value.service_request_event_tasks'));
		})->whereHas('subtype', function ($inner_query) { 
			$inner_query->whereIn('value', config('portiqo-crm.settings.lookup_value.service_request_event_tasks_modes'));
		});
	} //Function ends


	/**
	 * Show Events
	 */
	public function events()
	{
		return $this->hasMany(
			ServiceRequestEvent::class,
			'sr_id', 'id'
		)->with([
			'type','subtype'
		])->whereHas('type', function ($inner_query) { 
			$inner_query->where('value', config('portiqo-crm.settings.lookup_value.service_request_event_calendar'));
		})->whereHas('subtype', function ($inner_query) { 
			$inner_query->whereIn('value', config('portiqo-crm.settings.lookup_value.service_request_event_calendar_modes'));
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
            config('crmomni-class.class_model.note'),
            'reference_id', 'id'
        )
        ->with(['type', 'owner'])
        ->whereHas('type', function($inner_query){$inner_query->where('key', '=', 'entity_type_servicerequest');})
        ->orderBy('created_at', 'desc');
	} //Function ends
  

	/**
	 * Documents for the Service Request
	 */
	public function documents()
	{
		return $this->hasMany(
			config('crmomni-class.class_model.document'),
			'reference_id', 'id'
        )
        ->with(['type', 'owner'])
        ->whereHas('type', function($inner_query){$inner_query->where('key', '=', 'entity_type_servicerequest');})
        ->where('is_active', 1)
		->orderBy('created_at', 'desc');
	} //Function ends
    
    
    /**
	 * Organization
	 */
	public function organization()
	{
		return $this->belongsTo(
			config('crmomni-class.class_model.organization'),
			'contact_id', 'id'
		);
	} //Function ends

} //Trait ends
