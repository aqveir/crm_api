<?php

namespace Modules\Subscription\Transformers\Responses;

use Illuminate\Http\Resources\Json\Responses;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use Exception;

class SubscriptionMinifiedResource extends ResourceCollection
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
                $data = ($data instanceof Collection)?$data:collect($data);
                $response=$data->only([
                    'id', 'status', 'plan', 
                    'trial_start', 'trial_end'
                ])->toArray();

                $response['period_start'] = $data['current_period_start'];
                $response['period_end'] = $data['current_period_end'];
                $response['invoice'] = $data['latest_invoice'];

                array_push($objReturnValue, $response);
            } //Loop ends
        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
