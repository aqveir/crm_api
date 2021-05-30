<?php

namespace Modules\ServiceRequest\Transformers\Responses;

use Illuminate\Http\Resources\Json\Responses;
use Illuminate\Http\Resources\Json\JsonResource;

use Exception;

class TaskResource extends JsonResource
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
        $status = null;

        try {
            $this->load('type', 'subtype', 'servicerequest', 'assignee', 'priority', 'status', 'owner');

            $assignee = $this['assignee'];
            if (!empty($assignee)) {
                $assignee->makeVisible(['completed_at']);
            } //End if 
            
            $response = $this->only([
                'id', 'subject', 'description',
                'start_at', 'end_at', 'completed_at', 'last_updated_at',
                'type', 'subtype', 'servicerequest', 'priority', 'status', 'owner', 'assignee'
            ]);

            $objReturnValue = $response;

        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends
}
