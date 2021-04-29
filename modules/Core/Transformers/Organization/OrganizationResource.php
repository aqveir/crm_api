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
        $response = [
            'hash'              => $this->hash,
            'name'              => $this->name,
            'subdomain'         => $this->subdomain,
            'logo'              => $this->logo,
            'website'           => $this->website,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'phone_idd'         => $this->phone_idd,
            'country'           => $this->country,
            'industry'          => $this->industry,
            'configurations'    => $this->configurations,
            'last_updated_at'   => $this->last_updated_at,
            'users_count'       => $this->users_count,
        ];


        return $response;
    } //Function ends

} //Class ends
