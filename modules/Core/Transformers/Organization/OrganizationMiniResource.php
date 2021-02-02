<?php

namespace Modules\Core\Transformers\Organization;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationMiniResource extends JsonResource
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
            'hash' => $this->hash,
            'name' => $this->name,
            'subdomain' => $this->subdomain,
            'logo' => $this->logo,
            'users_count' => $this->users_count,
            'last_updated_at' => $this->last_updated_at
        ];
    } //Function ends

} //Class ends
