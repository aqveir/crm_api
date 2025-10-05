<?php

namespace Modules\ServiceRequest\Models\Traits\Action;

use Modules\ServiceRequest\Models\ServiceRequestSource;
use Modules\ServiceRequest\Models\Task;
use Modules\ServiceRequest\Models\Event as ServiceRequestEvent;

use App\Models\ServiceRequest\ServiceRequestPropFilters;
use App\Models\ServiceRequest\ServiceRequestRecommendations;

/**
 * Class ServiceRequest Action
 */
trait ServiceRequestAction
{

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

} //Trait ends
