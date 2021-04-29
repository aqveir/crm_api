<?php

namespace Modules\Core\Transformers\Organization;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        //Build response
        $response = $this->only([
            'hash', 'name', 'subdomain', 'logo',
            'website', 'contact_person_name', 'phone', 'phone_idd', 'email',
            'last_updated_at', 'users_count',
            'country', 'industry', 'configurations'
        ]);

        return $response;
    } //Function ends

} //Class ends
