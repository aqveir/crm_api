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
        $status = null;

        try {
            $this->load('organization', 'availability', 'availability.status', 'country', 'timezone');

            $availability = collect($this['availability']);
            if ((!empty($availability)) && (count($availability)>0)) {
                $status = collect($availability['status'])->only('key', 'display_value');
            } //End if

            //Get image path if exists
            $avatarPath = empty($this->avatar)?null:url(Storage::url($this->avatar));

            $response = $this->only([
                'hash', 'username',
                'first_name', 'last_name', 'full_name', 'name_initials',
                'email', 'phone', 'virtual_phone_number', 'language',
                'last_login_at', 'last_updated_at',
                'organization', 'country', 'timezone',
                'is_active', 'is_remote_access_only',
                'is_pool', 'is_default'
            ]);
            $response['roles'] = $this->roles($this->org_id)->get();
            $response['privileges'] = $this->privileges($this->org_id)->get();
            $response['is_verified'] = empty($this['verified_at'])?false:true;
            $response['avatar'] = $avatarPath;

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
