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
        $status = null;

        try {
            $objReturnValue = [];
            foreach ($this->collection as $data) {
                $data->load('availability', 'availability.status');
                $availability = collect($data['availability']);
                if (!empty($availability)) {
                    $status = collect($availability['status'])->only('key', 'display_value');
                } //End if

                $response = $data->only([
                    'hash', 'name_initials', 'full_name', 'email', 'phone',
                    'country',
                    'is_active', 'last_login_at', 'last_updated_at'
                ]);
                $response['avatar'] = 'assets/media/svg/avatars/001-boy.svg';
                $response['availability'] = $availability?$availability->only('last_updated_at'):null;
                $response['availability']['status'] = $status;

                array_push($objReturnValue, $response);
            } //Loop ends
        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
