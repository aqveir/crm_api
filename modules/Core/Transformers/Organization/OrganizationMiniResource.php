<?php

namespace Modules\Core\Transformers\Organization;

use Illuminate\Http\Resources\Json\Resource;

class OrganizationMiniResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
