<?php

namespace Modules\ServiceRequest\Models\Traits\Relationship;

use Log;


/**
 * Class ActivityParticipant Relationship
 */
trait ActivityParticipantRelationship
{

    /**
     * Participant Type
     */
	public function type()
	{
		return $this->hasOne(
			config('aqveir-class.class_model.lookup_value'),
			'id', 'participant_type_id'
		);
    } //Function ends
        
} //Trait ends
