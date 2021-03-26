<?php

namespace Modules\User\Transformers\Responses;

use Illuminate\Http\Resources\Json\Responses;
use Illuminate\Http\Resources\Json\JsonResource;

use Exception;

class UserResource extends JsonResource
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
            $this->load('organization', 'availability', 'country', 'timezone');

            $response = $this->only([
                'hash', 'username',
                'first_name', 'last_name', 'full_name', 'name_initials',
                'email', 'phone', 'virtual_phone_number',
                'last_login_at', 'last_updated_at',
                'organization', 'availability', 'country', 'timezone',
                'is_active', 'is_remote_access_only'
            ]);
            $response['is_verified'] = empty($this['verified_at'])?false:true;

            $objReturnValue = $response;

        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
