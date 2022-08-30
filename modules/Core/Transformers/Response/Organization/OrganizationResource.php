<?php

namespace Modules\Core\Transformers\Response\Organization;

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
        //Load dependencies
        $this->load(['industry', 'country', 'timezone', 'configurations', 'users']);

        //Get image path if exists
        $logoPath = empty($this->logo)?null:url(Storage::url($this->logo));

        //Build response
        $response = $this->only([
            'hash', 'name', 'subdomain', 'custom_domain', 'website',
            'address', 'locality', 'city', 'zipcode',
            'google_place_id', 'longitude', 'latitude',
            'contact_person_name', 'email', 'phone',
            'search_tags', 'last_updated_at', 'is_active', 
            'users_count',
            'industry', 'country', 'timezone', 'configurations'
        ]);
        $response['logo'] = $logoPath;

        //Get notification
        $response['notifications'] = $this->notifications;

        return $response;
    } //Function ends

} //Class ends
