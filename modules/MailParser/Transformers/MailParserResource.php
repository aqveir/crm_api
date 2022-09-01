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
            $orgId = null;
            $primaryPhone = $primaryEmail = true;
            $detailPhone = $detailEmail = null;
            $details = [];

            //Get Organization Information
            $organization = config('aqveir-class.class_model.organization')::where('hash', $request['key'])->first();

            //Set Phone Number
            $detailPhone = $this->getDataForParam($organization['id'], 'phone');
            if (!empty($detailPhone)) {
                array_push($details, $detailPhone);
            } //End if

            //Set Email Address
            $detailEmail = $this->getDataForParam($organization['id'], 'email');
            if (!empty($detailEmail)) {
                array_push($details, $detailEmail);
            } //End if
            
            $objReturnValue = [
                'org_id'            => $organization['id'],
                'first_name'        => $this->getDataForParam($organization['id'], 'first_name'),
                'last_name'         => $this->getDataForParam($organization['id'], 'last_name'),
                'details'           => $details,
                'notes'             => $this->getNoteData($organization['id'], $request->except(['key', 'remote', 'secret']))
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
	private function getDataForParam($orgId, $param, bool $isPrimary=false, bool $isVerified=false)
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
                                            'org_id'            => $orgId,
                                            'type_key'          => config('aqveir.settings.static.key.lookup_value.email'),
                                            'identifier'        => $email,
                                            'is_primary'        => $isPrimary,
                                            'is_verified'       => $isVerified
                                        ];
                                    } //End if
                                    break;

                                case 'phone':
                                    $phone = $this[$synonym];
                                    $objReturnValue = $this->fnProcessPhone($orgId, $phone, $isPrimary, $isVerified);
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
	private function getNoteData($orgId, $data, string $entityType='entity_type_contact')
	{	
		$objReturnValue = null;
		try {
            $objReturnValue = [];
            array_push($objReturnValue, [
                'org_id'        => $orgId,
                'entity_type'   => $entityType, 
                'reference_id'  => null, 
                'note'          => json_encode($data)
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
	private function fnProcessPhone($orgId, $phone, bool $isPrimary=false, bool $isVerified=false)
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
                    'org_id'            => $orgId,
                    'type_key'          => config('aqveir.settings.static.key.lookup_value.phone'),
                    'subtype_key'       => $phoneSubTypeKey,
                    'identifier'        => $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164),
                    'is_primary'        => $isPrimary,
                    'is_verified'       => $isVerified
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

} //Class ends
