<?php

namespace Modules\MailParser\Transformers\Zapier;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\JsonResource;

use Exception;

class MailParserResource extends JsonResource
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
        try {
            $firstName = $lastName = '';
            $identifierPhone = $identifierEmail = $phoneIdd = '';
            $primaryPhone = $primaryEmail = true;
            $detailPhone = $detailEmail = null;
            $details = [];

            // $callDuration = null;
            // $callDirection = (isset($this['Direction']) && ($this['Direction']=='incoming'))?'telephony_direction_incoming':'telephony_direction_outgoing';
            // $urlRecording = null;

            //Set Phone Number
            if (!empty($identifierPhone)) {
                $detailPhone = [
                    'type_key'          => config('omnichannel.settings.static.key.lookup_value.phone'),
                    'phone_idd'         => $phoneIdd,
                    'identifier'        => $identifierPhone,
                    'is_primary'        => $primaryPhone
                ];

                array_push($details, $detailPhone);
            } //End if

            //Set Email Address
            if (!empty($identifierEmail)) {
                $detailEmail = [
                    'type_key'          => config('omnichannel.settings.static.key.lookup_value.email'),
                    'identifier'        => $identifierEmail,
                    'is_primary'        => $primaryEmail
                ];

                array_push($details, $detailEmail);
            } //End if
            
            $objReturnValue = [
                'first_name'        => $firstName,
                'last_name'         => $lastName,
                'details'           => $details
            ];
        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends

    // 'first_name' => 'required|string|max:40',
    // 'middle_name' => 'nullable|string|max:40',
    // 'last_name' => 'nullable|string|max:40',
    // 'birth_at' => 'nullable|date|before:now',
    // 'aniversary_at' => 'nullable|date|after:birth_at',
    // 'type_key' => 'string|max:50',
    // 'gender_key' => 'string|max:50',
    // 'timezone_key' => 'string|max:50',
    // 'language_code' => 'sometimes|string',

    // 'extras' => 'nullable|json',
    // 'settings' => 'nullable|json',
    
    // 'details' => 'required',
    // 'details.*.type_key' => 'string|required_with:details',
    // 'details.*.subtype_key' => 'nullable|string|max:40',
    // 'details.*.phone_idd' => 'nullable|string|max:5',
    // 'details.*.identifier' => 'string|max:200|required_with:details',
    // 'details.*.is_primary' => 'boolean',

} //Class ends
