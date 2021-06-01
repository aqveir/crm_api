<?php

namespace Modules\ServiceRequest\Traits;

use Log;
use Exception;

/**
 * Class ParticipantTrait
 */
trait ParticipantTrait
{


	public function getParticipants($organization, array $assigneeList)
	{
        try {
            $assigneeCollection = null;
            if ($assigneeList && is_array($assigneeList) && count($assigneeList)>0) {
                $assigneeCollection = [];

                foreach ($assigneeList as $assignee) {
                    $assigneeData = [];

                    //Lookup Participant Type data
                    $lookupParticipantType = $this->lookupRepository->getLookUpByKey($organization['id'], $assignee['participant_type_key']);
                    if (empty($lookupParticipantType)) { throw new BadRequestHttpException(); } //End if
                    $assigneeData['participant_type_id'] = $lookupParticipantType['id'];

                    //Get participant by type
                    switch ($assignee['participant_type_key']) {
                        case 'communication_person_type_contact':
                            # code...
                            break;
                        case 'communication_person_type_user':
                        default:
                            //Get Participant User by Hash
                            $participantUser = $this->userRepository->getDataByHash($organization['id'], $assignee['participant_hash']);
                            if (empty($participantUser)) { throw new BadRequestHttpException(); } //End if
                            $assigneeData['participant_id'] = $participantUser['id'];

                            break;
                    } //Switch ends

                    array_push($assigneeCollection, $assigneeData);
                } //Loop ends
            } //End if

            return $assigneeCollection;
        } catch (Error $e) {
            throw $e;
        } //Try-cetch ends
    } //Function ends

} //Trait ends