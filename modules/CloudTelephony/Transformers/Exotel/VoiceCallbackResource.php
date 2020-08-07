<?php

namespace Modules\CloudTelephony\Transformers\Exotel;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\JsonResource;

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
        return [
            'transaction_id'    => $this->Sid,
            'status'            => $this->Status,
            'uri'               => isset($this['Uri'])?$this['Uri']:null,
            'recording_url'     => isset($this['RecordingUrl'])?$this['RecordingUrl']:null,
            'updated_at'        => isset($this['DateUpdated'])?$this['DateUpdated']:null,
            'duration'          => isset($this['Duration'])?$this['Duration']:0,
            'started_at'        => isset($this['StartTime'])?$this['StartTime']:null,
            'to_number'         => '01010101010',
            'from_number'       => '09090909090',
        ];
    }
}
