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
            $this->load('organization', 'availability', 'availability.status', 'country', 'timezone');
            $availability = collect($this['availability']);
            if (!empty($availability)) {
                $status = collect($availability['status'])->only('key', 'display_value');
            } //End if

            $response = $this->only([
                'hash', 'username', 'avatar',
                'first_name', 'last_name', 'full_name', 'name_initials',
                'email', 'phone', 'virtual_phone_number',
                'last_login_at', 'last_updated_at',
                'organization', 'country', 'timezone',
                'is_active', 'is_remote_access_only',
                'is_pool', 'is_default'
            ]);
            $response['is_verified'] = empty($this['verified_at'])?false:true;

            $response['avatar'] = 'assets/media/svg/avatars/001-boy.svg';

            //Manage User Availability/Online Status
            $response['availability'] = $availability?$availability->only('last_updated_at'):null;
            $response['availability']['status'] = $status;

            $objReturnValue = $response;

        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
