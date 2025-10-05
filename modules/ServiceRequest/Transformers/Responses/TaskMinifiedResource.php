<?php

namespace Modules\ServiceRequest\Transformers\Responses;

use Illuminate\Http\Resources\Json\Responses;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

use Exception;

class TaskMinifiedResource extends ResourceCollection
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
                $data->load('type', 'subtype', 'servicerequest', 'assignee', 'priority', 'status', 'owner');

                $assignee = $data['assignee'];
                if (!empty($assignee)) {
                    $assignee->makeVisible(['completed_at']);
                } //End if                    

                $response = $data->only([
                    'id', 'subject', 'description',
                    'start_at', 'end_at', 'completed_at', 'last_updated_at',
                    'type', 'subtype', 'servicerequest', 'priority', 'status', 'owner', 'assignee'
                ]);

                array_push($objReturnValue, $response);
            } //Loop ends
        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
