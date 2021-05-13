<?php

namespace Modules\ServiceRequest\Transformers\Responses;

use Illuminate\Http\Resources\Json\Responses;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

use Exception;

class ServiceRequestMinifiedResource extends ResourceCollection
{

    public function __construct($collection)
    {
       parent::__construct($collection);
    }


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
            $objReturnValue = [];
            foreach ($this->collection as $data) {
                $data->load('contact', 'account', 'owner', 'category', 'type', 'status', 'stage', 'sources');
                $data->loadCount('tasks', 'events', 'notes');

                $response = $data->only([
                    'hash', 'last_updated_at', 'star_rating',
                    'contact', 'account', 'owner', 
                    'category', 'type', 'status', 'stage', 'sources',
                    'tasks_count', 'events_count', 'notes_count',
                ]);

                array_push($objReturnValue, $response);
            } //Loop ends
        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
