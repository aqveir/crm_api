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
        return [
            'hash'              => $this->hash,
            'name'              => $this->name,
            'sub_domain'        => $this->sub_domain,
            'logo'              => $this->logo,
            'website'           => $this->website,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'country'           => $this->country,
            'industry'          => $this->industry,
            'users'             => $this->users,
            'configurations'    => $this->configurations,
            'last_updated_at'   => $this->last_updated_at
        ];
    } //Function ends

} //Class ends
