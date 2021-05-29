<?php

namespace Modules\ServiceRequest\Models\Traits\Action;

use Log;
use Exception;


/**
 * Class ActivityParticipant Action
 */
trait ActivityParticipantAction
{

    /**
     * Participant
     */
	public function getParticipantAttribute()
	{
        try {
            $participantTypeId = $this->participant_type_id;
            $participantType = config('crmomni-class.class_model.lookup_value')::where('id', $participantTypeId)->first();

            if (!empty($participantType)) {
                switch ($participantType['key']) {
                    case 'communication_person_type_contact':
                        return config('crmomni-class.class_model.contact.main')::where('id', $this->participant_id)->first();
                        break;
                    
                    case 'communication_person_type_user':
                    default:
                        return config('crmomni-class.class_model.user.main')::where('id', $this->participant_id)->first();
                    break;
                } //Switch ends
            } //End if
        } catch(Exception $e) {
            throw $e;
        } //Try-catch ends
	} //Function ends
    
} //Trait ends
