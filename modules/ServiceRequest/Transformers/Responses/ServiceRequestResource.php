<?php

namespace Modules\ServiceRequest\Transformers\Responses;

use Illuminate\Http\Resources\Json\Responses;
use Illuminate\Http\Resources\Json\JsonResource;

use Exception;

class ServiceRequestResource extends JsonResource
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
            $this->load('contact', 'account', 'owner', 'type', 'status', 'stage', 'sources');
            $this->loadCount('tasks', 'events', 'notes', 'documents');
            
            $response = $this->only([
                'hash', 'last_updated_at',
                'contact', 'account', 'owner', 
                'type', 'status', 'stage', 'sources',
                'tasks_count', 'events_count', 'notes_count', 'documents_count'
            ]);

            //Transform Owner Data
            $owner = $this['owner'];
            $response['owner']['hash'] = $owner['hash'];
            $response['owner']['avatar'] = $owner['avatar'];
            $response['owner']['name_initials'] = $owner['name_initials'];
            $response['owner']['full_name'] = $owner['full_name'];

            $objReturnValue = $response;

        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends
}
