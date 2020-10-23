<?php

namespace Modules\User\Transformers\Responses;

use Illuminate\Http\Resources\Json\Responses;
use Illuminate\Http\Resources\Json\JsonResource;

use Exception;

class UserAvailabilityResource extends JsonResource
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

        $user=[];
        $user['hash'] = $this['user']['hash'];
        $user['username'] = $this['user']['username'];
        $user['full_name'] = $this['user']['full_name'];

        $status=[];
        $status['id'] = $this['status']['id'];
        $status['key'] = $this['status']['key'];
        $status['display_value'] = $this['status']['display_value'];

        try {
            $objReturnValue = [
                'user'              => $user,
                'status'            => $status,
                'last_updated_at'   => $this['last_updated_at'],
            ];
        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
