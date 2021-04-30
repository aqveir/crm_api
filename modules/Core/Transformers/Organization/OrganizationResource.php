<?php

namespace Modules\Core\Transformers\Organization;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
        $logoPath = asset(Storage::url($this->logo));

        //Build response
        $response = $this->only([
            'hash', 'name', 'subdomain',
            'website', 'contact_person_name', 'phone', 'phone_idd', 'email',
            'last_updated_at', 'users_count',
            'country', 'industry', 'configurations'
        ]);
        $response['logo'] = $logoPath;

        return $response;
    } //Function ends

} //Class ends
