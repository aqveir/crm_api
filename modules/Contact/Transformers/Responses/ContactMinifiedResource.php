<?php

namespace Modules\Contact\Transformers\Responses;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\Responses;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

use Exception;

class ContactMinifiedResource extends ResourceCollection
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
                $data->load(['type']);

                //Get image path if exists
                $avatarPath = empty($data->avatar)?null:url(Storage::url($data->avatar));

                $response = $data->only([
                    'hash', 'name_initials', 'full_name', 'type',
                    'is_verified', 'is_active', 'last_updated_at'
                ]);
                $response['avatar'] = $avatarPath;

                array_push($objReturnValue, $response);
            } //Loop ends
        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
