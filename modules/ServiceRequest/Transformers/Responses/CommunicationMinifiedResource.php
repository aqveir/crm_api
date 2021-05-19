<?php

namespace Modules\ServiceRequest\Transformers\Responses;

use Illuminate\Http\Resources\Json\Responses;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

use Exception;

class CommunicationMinifiedResource extends ResourceCollection
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
                $data->load('type', 'direction');
                //$data->loadCount('tasks', 'events', 'notes');

                $response = $data->only([
                    'type', 'direction'
                ]);

                array_push($objReturnValue, $response);
            } //Loop ends
        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
