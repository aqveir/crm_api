<?php

namespace Modules\CloudTelephony\Transformers\Twilio\Responses;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\JsonResource;

use Exception;

class VoiceCallDetailsResource extends JsonResource
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
            $callDirection = (isset($this['direction']) && ($this['direction']=='inbound'))?'telephony_direction_incoming':'telephony_direction_outgoing';
            $urlRecording = null;

            //Call Status
            $callStatus = (isset($this['status']))?$this['status']:null;
            switch ($callStatus) {
                case 'queued':
                case 'ringing':
                    $callStatus = 'telephony_call_status_type_queued';
                    break;

                case 'in-progress':
                    $callStatus = 'telephony_call_status_type_in_progress';
                    break;

                case 'completed':
                    $callStatus = 'telephony_call_status_type_completed';
                    $callDuration = (isset($this['Duration']))?($this['Duration']):0;
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
                'transaction_id'    => $this['sid'],
                'direction'         => $callDirection,
                'source'            => 'twilio',
                'status'            => $callStatus,
                'duration'          => isset($this['duration'])?$this['duration']:null,
                'recording_url'     => $urlRecording,
                'uri'               => isset($this['uri'])?$this['uri']:null,
                'created_at'        => isset($this['start_time'])?$this['start_time']:null,
                'updated_at'        => isset($this['date_updated'])?$this['date_updated']:null,
                'to_number'         => isset($this['to'])?$this['to']:null,
                'from_number'       => isset($this['from'])?$this['from']:null,
                'sid_number'        => isset($this['phone_number_sid'])?$this['phone_number_sid']:null,
                // 'proxy'             => isset($this['digits'])?$this['digits']:'0',
                // 'whom_number'       => ''
            ];
        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends
}
