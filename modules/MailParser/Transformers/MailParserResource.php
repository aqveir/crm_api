<?php

namespace Modules\MailParser\Transformers;

use Illuminate\Support\Facades\Log;

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
            $primaryPhone = $primaryEmail = true;
            $detailPhone = $detailEmail = null;
            $details = [];

            //Set Phone Number
            $detailPhone = $this->getDataForParam('phone');
            if (!empty($detailPhone)) {
                array_push($details, $detailPhone);
            } //End if

            //Set Email Address
            $detailEmail = $this->getDataForParam('email');
            if (!empty($detailEmail)) {
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
	 */
	private function getDataForParam($param, bool $isPrimary=false, bool $isVerified=false)
	{	
		$objReturnValue = null;
		try {
            $keySynonyms = config('mailparser.settings.parse.synonyms.' . $param);

            if (!empty($keySynonyms)) {
                if ((is_array($keySynonyms)) && (count($keySynonyms)>1)) {
                    foreach ($keySynonyms as $synonym) {
                        if (isset($this[$synonym])) { 
                            switch ($param) {
                                case 'email':
                                    $email = $this[$synonym];
                                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                        $objReturnValue = [
                                            'type_key'          => config('aqveir.settings.static.key.lookup_value.email'),
                                            'identifier'        => $email,
                                            'is_primary'        => $isPrimary
                                        ];
                                    } //End if
                                    break;

                                case 'phone':
                                    $phone = $this[$synonym];
                                    $objReturnValue = $this->fnProcessPhone($phone, $isPrimary, $isVerified);
                                    break;
                                
                                default:
                                    $objReturnValue = $this[$synonym];
                                    break;
                            } //End switch

                            break;
                        } //End if
                    } //Loop ends
                } //End if
            } //End if
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


	/**
	 * Process phone number
	 *
	 * @return objReturnValue
	 */
	private function fnProcessPhone($phone, bool $isPrimary=false, bool $isVerified=false)
	{	
		$objReturnValue = null;
		try {
			$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

            //Parse phone number
			$phoneNumberObject = $phoneNumberUtil->parse($phone, null);
            if ($phoneNumberUtil->isValidNumber($phoneNumberObject)) {

                //Get phone type
                $phoneSubTypeKey = null;
                switch ($phoneNumberUtil->getNumberType($phoneNumberObject)) {
                    case \libphonenumber\PhoneNumberType::MOBILE:
                        $phoneSubTypeKey = 'contact_detail_subtype_phone_mobile';
                        break;

                    case \libphonenumber\PhoneNumberType::FIXED_LINE:
                        $phoneSubTypeKey = 'contact_detail_subtype_phone_landline';
                        break;
                    
                    default:
                        break;
                } //Switch ends

                $objReturnValue = [
                    'type_key'          => config('aqveir.settings.static.key.lookup_value.phone'),
                    'subtype_key'       => $phoneSubTypeKey,
                    'identifier'        => $phoneNumberObject->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164),
                    'is_primary'        => $isPrimary
                ];
            } //End if	    
		} catch (\libphonenumber\NumberParseException $e) {
		    log::error($e);
		    $objReturnValue = null;
		} catch (Exception $e) {
			Log::error(json_encode($e));
			$objReturnValue = null;
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
