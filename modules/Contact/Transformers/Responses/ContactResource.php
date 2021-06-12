<?php

namespace Modules\Contact\Transformers\Responses;

use Illuminate\Http\Resources\Json\Responses;
use Illuminate\Http\Resources\Json\JsonResource;

use Exception;

class ContactResource extends JsonResource
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
            $this->load(['type', 'gender', 'group', 'status', 'details', 'addresses', 'notes', 'documents']);

            //Get image path if exists
            $avatarPath = empty($this->avatar)?null:url(Storage::url($this->avatar));

            $response = $this->only([
                'id', 'hash', 'full_name', 'name_initials',
                'first_name','middle_name','last_name',
                'date_of_birth_at',
                'type', 'gender', 'group', 'status', 
                'details', 'addresses', 'notes', 'documents',
                'is_verified', 'is_active', 'last_updated_at'
            ]);
            $response['avatar'] = $avatarPath;

            $objReturnValue = $response;
        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
