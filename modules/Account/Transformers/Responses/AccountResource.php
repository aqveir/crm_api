<?php

namespace Modules\Account\Transformers\Responses;

use Illuminate\Http\Resources\Json\Responses;
use Illuminate\Http\Resources\Json\JsonResource;

use Exception;

class AccountResource extends JsonResource
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
            $this->load('type', 'owner', 'timezone', 'state', 'country');
            
            $response = $this->only([
                'id', 'hash', 'name', 'description', 
                'type', 'owner',
                'address', 'locality', 'city', 'zipcode',
                'state', 'country', 'timezone', 
                'google_place_id', 'longitude', 'latitude',
                'website', 'email', 'phone',
                'is_default', 'last_updated_at'
            ]);

            $objReturnValue = $response;

        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
