<?php

namespace Modules\User\Transformers\Responses;

use Illuminate\Http\Resources\Json\Responses;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

use Exception;

class UserMinifiedResource extends ResourceCollection
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

        // $user=[];
        // $user['hash'] = $this['user']['hash'];
        // $user['username'] = $this['user']['username'];
        // $user['full_name'] = $this['user']['full_name'];

        // $status=[];
        // $status['id'] = $this['status']['id'];
        // $status['key'] = $this['status']['key'];
        // $status['display_value'] = $this['status']['display_value'];

        try {
            $objReturnValue = [];
            foreach ($this->collection as $data) {
                $response = $data->only([
                    'hash', 'name_initials', 'full_name', 'email', 'phone',
                    'country',
                    'is_active', 'last_login_at', 'last_updated_at'
                ]);
                $response['avatar'] = null;

                array_push($objReturnValue, $response);
            } //Loop ends
        } catch(Exception $e) {
            $objReturnValue=null;
            throw $e;
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
