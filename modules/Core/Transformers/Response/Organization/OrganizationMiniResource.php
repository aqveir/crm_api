<?php

namespace Modules\Core\Transformers\Response\Organization;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\Responses;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
//use Intervention\Image\Facades\Image;

use Exception;

class OrganizationMiniResource extends ResourceCollection
{

    public function __construct($collection)
    {
       parent::__construct($collection);
    }


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
            $objReturnValue = [];
            foreach ($this->collection as $data) {

                $data->load(['users', 'configurations']);

                //Get image path if exists
                $logoPath = empty($data->logo)?null:url(Storage::url($data->logo));

                $response = $data->only(
                    'hash', 'name', 'subdomain',
                    'users_count', 'last_updated_at', 'is_active'
                );

                $response['logo'] = $logoPath;

                array_push($objReturnValue, $response);
            } //Loop ends
        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
