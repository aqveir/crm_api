<?php

namespace Modules\MailParser\Transformers;

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
    public function toArray($request, string $provider='default')
    {
        $objReturnValue=null;
        try {
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
                'first_name'        => $this->getDataForParam('first_name'),
                'last_name'         => $this->getDataForParam('last_name'),
                'details'           => $details,
                'notes'             => $this->getNoteData($request->all())
            ];
        } catch(Exception $e) {
            $objReturnValue=null;
        }
        return $objReturnValue;
    } //Function ends


    /**
	 * Parse data based on the data type and return matching value
	 *
	 * @return objReturnValue
	 *
	 */
	private function getDataForParam($param)
	{	
		$objReturnValue = null;
		try {
            $keySynonyms = config('mailparser.settings.parse.synonyms.' . $param);

            if (!empty($keySynonyms)) {
                if ((is_array($keySynonyms)) && (count($keySynonyms)>1)) {
                    foreach ($keySynonyms as $synonym) {
                        if (isset($this[$synonym])) { 
                            $objReturnValue = $this[$synonym]; 
                        } //End if
                    } //Loop ends
                } //End if
            } //End if

            

  			//Handle FIRST NAME Parameter in Request
            // if (isset($this['name_first'])) { $objReturnValue = $this['name_first']; } //End if
            // if (isset($this['first_name'])) { $objReturnValue = $this['first_name']; } //End if
            // if (isset($this['firstname']))  { $objReturnValue = $this['firstname'];  } //End if
            // if (isset($this['firstName']))  { $objReturnValue = $this['firstName'];  } //End if

            // if ((!empty($objReturnValue)) && (strlen($objReturnValue)>1)) {
            //     $strData = $this->fnCleanNamesData($objReturnValue);
            //     if($strData == 'Not Provided' || strlen($strData)<1) {
            //         $objReturnValue = ''; 
            //     } else {
            //         $objReturnValue = $strData;
            //     } //End if-else
            // } //End if
		} catch (Exception $e) {
	        throw $e;
		} //Try-catch ends

		return $objReturnValue;
	} //Function ends


    /**
	 * Convert the request into a raw note so that actual data is
     * available for future reference
	 *
	 * @return objReturnValue
	 *
	 */
	private function getNoteData($data, string $entityType='entity_type_contact')
	{	
		$objReturnValue = null;
		try {
            $objReturnValue = [];
            array_push($objReturnValue, [
                'entity_type'   => $entityType, 
                'reference_id'  => null, 
                'note'          => json_encode($data), 
                'org_id'        => null, 
                'created_by'    => 0
            ]);
		} catch (Exception $e) {
	        throw $e;
		} //Try-catch ends

		return $objReturnValue;
	} //Function ends


    /**
	 * Remove Special fbsql_set_characterset(link_identifier, characterset) From String
	 *
	 * @return objReturnValue
	 *
	 */
	private function fnCleanNamesData($string)
	{	
		$objReturnValue = null;
		try {	
			$string = $string;
			$cleanString = preg_replace("/[^a-zA-Z ']/", "", $string);

			$objReturnValue = $cleanString;
		} catch (Exception $e) {
	        throw $e;
		} //Try-catch ends

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
