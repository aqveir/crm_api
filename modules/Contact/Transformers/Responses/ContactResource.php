<?php

namespace Modules\Contact\Transformers\Responses;

use Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\Responses;
use Illuminate\Http\Resources\Json\JsonResource;

use Exception;

class ContactResource extends JsonResource
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
        $status = null;

        try {
            //Get current user and get privilege
            $user = Auth::guard('backend')->user();
            $isMaskedData = $user?!($user->hasPrivileges(['show_contact_unmasked_data'], true)):true;

            $this->load(['type', 'gender', 'group', 'details', 'addresses', 'notes', 'documents', 
                'service_requests', 'service_requests.status', 'service_requests.category'
            ]);
            $this->loadCount(['notes', 'documents', 'service_requests']);

            //Get image path if exists
            $avatarPath = empty($this->avatar)?null:url(Storage::url($this->avatar));

            $response = $this->only([
                'id', 'hash', 'full_name', 'name_initials',
                'first_name','middle_name','last_name',
                'birth_at', 'type', 'gender', 'group',

                'addresses', 'notes', 'documents',
                'is_verified', 'is_active', 'last_updated_at',
                'notes_count', 'documents_count', 'service_requests_count'
            ]);
            $response['avatar'] = $avatarPath;
            $response['service_requests'] = $this['service_requests'];
            $response['extras'] = json_encode($this['extras']);
            $response['settings'] = json_encode($this['settings']);

            //Conatct details
            $details = $this->details->toArray();

            //Mask data, is applicable
            if ($isMaskedData) {
                //Check if the details exist
                if ($details && is_array($details) && (count($details)>0)) {
                    //Iterate the details
                    foreach ($details as &$detail) {
                        $identifier = $detail['identifier'];

                        //Mask the identifier based on the detail type
                        switch ($detail['type']['key']) {
                            case 'contact_detail_type_email':
                                $posStart = 2;
                                $lengthOfTrailUnchanged = 4;

                                $detail['identifier'] = substr_replace($identifier, str_repeat('X', (strlen($identifier)-($lengthOfTrailUnchanged+$posStart))), $posStart, (strlen($identifier)-($lengthOfTrailUnchanged+$posStart)));
                                break;

                            case 'contact_detail_type_phone':
                                $posStart = 2;
                                $lengthOfTrailUnchanged = 2;

                                $detail['identifier'] = substr_replace($identifier, str_repeat('X', (strlen($identifier)-($lengthOfTrailUnchanged+$posStart))), $posStart, (strlen($identifier)-($lengthOfTrailUnchanged+$posStart)));
                                break;
                            
                            default:
                                # code...
                                break;
                        } //Switch ends
                    } //Loop ends
                } //End if
            } //End if

            $response['details'] = $details;

            $objReturnValue = $response;
        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends

} //Class ends
