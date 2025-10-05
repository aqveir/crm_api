<?php

namespace Modules\CloudTelephony\Transformers\Twilio;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\JsonResource;

use Exception;

class VoiceCallbackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $objReturnValue=null;
        try {
            $callDuration = null;
            $callDirection = (isset($this['Direction']) && ($this['Direction']=='incoming'))?'telephony_direction_incoming':'telephony_direction_outgoing';
            $urlRecording = null;

            //Call Status
            $callStatus = (isset($this['Status']))?$this['Status']:null;
            switch ($callStatus) {
                case 'queued':
                    $callStatus = 'telephony_call_status_type_queued';
                    break;

                case 'ringing':
                    $callStatus = 'telephony_call_status_type_queued';
                    break;

                case 'in-progress':
                    $callStatus = 'telephony_call_status_type_in_progress';
                    break;

                case 'completed':
                    $callStatus = 'telephony_call_status_type_completed';
                    $callDuration = (isset($this['ConversationDuration']))?($this['ConversationDuration']):0;
                    $urlRecording = (isset($this['RecordingUrl']))?($this['RecordingUrl']):null;
                    break;

                case 'failed':
                    $callStatus = 'telephony_call_status_type_failed';
                    break;

                case 'busy':
                    $callStatus = 'telephony_call_status_type_busy';
                    break;

                case 'no-answer':
                    $callStatus = 'telephony_call_status_type_no_answer';
                    break;
                
                default:
                    $callStatus = 'telephony_call_status_type_failed';
                    break;
            } //End Switch
            
            $objReturnValue = [
                'transaction_uuid'  => $this['CallSid'],
                'direction_key'     => $callDirection,
                'call_status_key'   => $callStatus,
                'call_recording_url'=> $urlRecording,
                'call_duration'     => $callDuration,
                'uri'               => isset($this['Uri'])?$this['Uri']:null,
                'created_at'        => isset($this['DateCreated'])?$this['DateCreated']:null,
                'updated_at'        => isset($this['DateUpdated'])?$this['DateUpdated']:null,
                'to_number'         => isset($this['To'])?$this['To']:null,
                'from_number'       => isset($this['From'])?$this['From']:null,
                'virtual_number'    => isset($this['PhoneNumberSid'])?$this['PhoneNumberSid']:null,
            ];
        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends

    // return [
    //     'transaction_id'    => $this['CallSid'],
    //     'status'            => $this->Status,
    //     'uri'               => isset($this['Uri'])?$this['Uri']:null,
    //     'recording_url'     => isset($this['RecordingUrl'])?$this['RecordingUrl']:null,
    //     'updated_at'        => isset($this['DateUpdated'])?$this['DateUpdated']:null,
    //     'duration'          => isset($this['Duration'])?$this['Duration']:0,
    //     'started_at'        => isset($this['StartTime'])?$this['StartTime']:null,
    //     'to_number'         => '01010101010',
    //     'from_number'       => '09090909090',
    // ];

} //Class ends
