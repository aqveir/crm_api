<?php

namespace Modules\Contact\Models\Contact\Traits\Action;

use Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use App\Events\Contact\ContactCreateEvent;

use Exception;
use Modules\Contact\Models\Contact\Contact;
use Modules\Contact\Models\Contact\ContactAddress;
use Modules\Contact\Models\Contact\ContactDetail;
use App\Models\ServiceRequest\ServiceRequestEvent;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use App\Exceptions\DuplicateException;

/**
 * Action methods on Contact
 */
trait ContactAction
{
    
    /**
     * Get Contact Details by Type Key
     * 
     * @param  \string  $typeKey
     * @param  \array  $subtypeKeys
     * @param  \mixed  $isActive
     * @param  \mixed  $isPrimary
     * @param  \mixed  $isVerified
     */
    public function getContactDetailsByKey(string $typeKey, array $subtypeKeys=null, $isActive=null, $isPrimary=null, $isVerified=null)
    {
		$objReturnValue=null;
		try {
            $query = $this->details()
                ->with(['type', 'subtype'])
                ->whereHas('type', function($inner_query) use ($typeKey) { $inner_query->where('key', $typeKey); })
                ->whereHas('subtype', function($inner_query) use ($subtypeKeys) { $inner_query->whereIn('key', $subtypeKeys); });

            //Active check
            if (!empty($isActive)) { $query = $query->where('is_active', $isActive); }

            //Primary check
            if (!empty($isPrimary)) { $query = $query->where('is_primary', $isPrimary); }

            //Verified check
            if (!empty($isVerified)) { $query = $query->where('is_verified', $isVerified); }
                
            $query = $query->firstOrFail();

            //Get the Contact Object
            $objReturnValue = $query;
		} catch (Exception $e) {
			$objReturnValue=null;
			throw $e;
		} //Try-Catch ends
		return $objReturnValue;
    } //Function ends
    public function getContactDetailsForEmail($isActive=null, $isPrimary=null) {
        return $this->getContactDetailsByKey(
            'contact_detail_type_email', 
            ['contact_detail_subtype_email_work', 'contact_detail_subtype_email_personal'],
            $isActive, $isPrimary
        );
    } //Function ends



	/**
	* Get Contact by Hash
	*/
	public function getContactByHash(int $orgId, String $hash)
	{
		$objReturnValue=null;
		try {
			$query = config('aqveir-class.class_model.contact.main')::where('hash', '=', $hash);
			$query = $query->where('org_id', '=', $orgId);
			$query = $query->orderBy('id', 'asc')->firstOrFail();

            //Get the Contact Object
            $objReturnValue = $query;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-Catch ends
		return $objReturnValue;
	} //Function ends


	/**
	* Get Contact by Identifier
	*/
	public function getContactById(int $orgId, int $id)
	{
		$objReturnValue=null;
		try {
			$query = config('aqveir-class.class_model.contact.main')::where('id', $id);
			if($orgId>0) { $query = $query->where('org_id', $orgId); }
			$query = $query->orderBy('id', 'asc')->firstOrFail();

            //Get the Contact Object
            $objReturnValue = $query;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error(json_encode($e));
		} //Try-Catch ends
		return $objReturnValue;
	} //Function ends


	/**
	* Create New Contact For Unknown Number
	*/
	public function createNewContact(int $orgId, $request, int $createdBy=0, bool $skipDuplicateCheck=false, bool $returnContact=false)
	{
		$objReturnValue=null; $getDuplicateContact=false;
		try {
            //Get Type LookUp For Phone
            $typePhone = $this->getLookUpByValue($orgId, config('portiqo-crm.settings.lookup_value.phone'));
            //Log::debug($typePhone);

            if(isset($request['Contact']['details']))
            {
                //Duplicate Contact Identifier Check
                if(!$skipDuplicateCheck) {
                    foreach($request['Contact']['details'] as $ContactDetail) {   
                        $identifier=$ContactDetail['identifier'];
                        $typeId=$ContactDetail['type'];

                        //Check Duplicate for Phone
                        if(($typeId==$typePhone->id) && !$getDuplicateContact) {
                            $getDuplicateContact = $this->checkDuplicateIdentifier($typeId, $identifier, null, $orgId);
                            Log::debug('Check Duplicate ->'.json_encode($getDuplicateContact));
                        } //End if
                    } //Loop End
             
                    if($getDuplicateContact) {
                        if($returnContact) {
                            log::debug('Return Duplicate Contact ->' . json_encode($getDuplicateContact->Contact, JSON_PRETTY_PRINT));
                            return $getDuplicateContact->Contact;
                        } else {
                            //Throw Duplicate Exception
                            throw new DuplicateException();                            
                        } //End if-else
                    } //End if              
                } //End if

                $Contact = $this->createContact($orgId, $request, $createdBy);
                if(!$Contact && (!$Contact->id>0)) { throw new HttpException(500); } //End if

                $isSaveSuccess = $this->saveContactAddress($Contact, $request, $createdBy);
                $isSaveSuccess = $this->saveContactDetails($Contact, $request, $createdBy);
                //log::debug('ContactDetail ->'. json_encode($isSaveSuccess));

                //Clear cache
                $keyCache = config('portiqo-crm.settings.cache.Contacts.key').'_'.(string)$orgId;
                if (Cache::has($keyCache)) { Cache::forget($keyCache); } //End if

                $objReturnValue = $Contact;     
            } else {
                throw new BadRequestHttpException();
            } //End if-else
    	} catch (DuplicateException $e) {
            Log::error(json_encode($e));
            throw new DuplicateException();
        } catch (Exception $e) {
            Log::error(json_encode($e));
            throw new HttpException(500);
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends


	/**
	* Create New Contact based on Request Data
	*
	* @return object
	*/
    public function createContact(int $orgId, $request, int $createdBy=0) {
        $objReturnValue=null;
        try {
            $companyId = $this->getCompany($request);

            //Get request parameters and save
            $Contact = new Contact($request['Contact']);
            $Contact->org_id=$orgId;
            if(isset($companyId) && $companyId>0) { $Contact->company_id=$companyId; }

            $Contact->created_by=$createdBy;
            if(!$Contact->save()) {
                throw new HttpException(500);
            } //End if

            //Get Contact By ContactId (to get Hash Value)
            $newContact = $this->getContactById($orgId, $Contact->id);
            //Log::debug($newContact);

            //Create Contact From Events
            event(new ContactCreateEvent($Contact));
                
            //Get the Newly Created Contact
            $objReturnValue = $newContact;
        } catch (Exception $e) {
            Log::error(json_encode($e));
            $objReturnValue=null;
        } //Try-Catch ends
        return $objReturnValue;
    } //Function ends


    /**
     * Check Duplicate Identifier
     */
    public function checkDuplicateIdentifier($typeId, $identifier, $isPrimary=null, $orgId=null, int $ContactId=0)
    {
        $objReturnValue = null;
        try {
            $query = ContactDetail::where('type', $typeId);
            //if($isPrimary) { $query = $query->where('is_primary', $isPrimary); } //End if
            if($orgId>0) { $query = $query->where('org_id', $orgId); } //End if
            if($ContactId>0) { $query = $query->whereNotIn('Contact_id', [$ContactId]); } //End if
            $query = $query->where('identifier','like', '%' . $identifier. '%')->firstOrFail();
             
            $objReturnValue = $query;              
        } catch (Exception $e) {
            $objReturnValue=null;
            Log::error(json_encode($e));
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends


	/**
	* Save Contact Address based on Request Data
	*
	* @return boolean
	*/
    public function saveContactAddress($Contact, $request, $createdBy=0) {
        $objReturnValue=false;
        try {
            if(isset($request->Contact['addresses']) && (count($request->Contact['addresses'])>0)) {
                foreach($request->Contact['addresses'] as $reqContactAddress) {
                    if(isset($reqContactAddress['id']) && $reqContactAddress['id']>0) { //Update
                        $ContactAddress = ContactAddress::find($reqContactAddress['id']);
                        $ContactAddress->fill($reqContactAddress);
                    } else { //Create
                        $ContactAddress = new ContactAddress($reqContactAddress);
                        $ContactAddress->Contact_id=$Contact->id;
                        $ContactAddress->org_id=$Contact->org_id;
                    } //End if-else

                    if(!$ContactAddress->save()) {
                        $objReturnValue=false;
                        throw new HttpException(500);
                    } //End if
                    $objReturnValue = true;
                } //Loop ends
            } else {
                $objReturnValue = true;
            } //End if-else
        } catch (Exception $e) {
            Log::error(json_encode($e));
            $objReturnValue=false;
        } //Try-Catch ends
        
        return $objReturnValue;
    } //Function ends


	/**
	* Save Contact Details based on Request Data
	*
	* @return boolean
	*/
    public function saveContactDetails($Contact, $request, $createdBy=0) {
        $objReturnValue=false;
        try {
            if(isset($request['Contact']['details']) && (count($request['Contact']['details'])>0)){
                //Get orgId From Contact
                $orgId = $Contact->org_id;

                //Get Type LookUp For Phone
                $typePhone = $this->getLookUpByValue($orgId, config('portiqo-crm.settings.lookup_value.phone'));
                //Log::debug($typePhone);
                    
                foreach($request['Contact']['details'] as $reqContactDetail) {  
                    //Get Country Code for Phone
                    $typeId=$reqContactDetail['type'];
                    //log::debug($typeId);

                    //Check Type Equality
                    if($typeId==$typePhone['id']) {
                        $country = null;

                        //Assign Country Code
                        if(isset($reqContactDetail['country_iso_code'])) {
                            $countryCode = $reqContactDetail['country_iso_code'];

                            //Update Or Create CountryCode
                            $country = $this->updateOrCreateCountryCode($countryCode);
                        } //End if
                        
                        //Set country code
                        $reqContactDetail['country_code'] = ($country)?$country['id']:1;
                        //log::debug($reqContactDetail['country_code']);
                    } //End if

                    if(isset($reqContactDetail['id']) && $reqContactDetail['id']>0) { 
                        //Update Existing Contact Details
                        $ContactDetail = ContactDetail::find($reqContactDetail['id']);
                        $ContactDetail->fill($reqContactDetail);
                    } else { 
                        //Create New Contact Details
                        $ContactDetail = new ContactDetail($reqContactDetail);
                        $ContactDetail->Contact_id  = $Contact->id;
                        $ContactDetail->org_id      = $Contact->org_id;
                    } //End if-else 

                    if(!$ContactDetail->save()) {
                        $objReturnValue=false;
                        throw new HttpException(500);
                    } //End if

                    $objReturnValue = true;
                } //Loop ends
            } else {
                $objReturnValue = true;
            } //End if-else
        } catch (Exception $e) {
            Log::error(json_encode($e));
            $objReturnValue=false;
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends


	/**
	* Get Company Information based on Request Data
	*
	* @return number
	*/
    public function getCompany($request) {
        $companyId=0;
        if(isset($request->Contact['company']['name'])) {
            //Create Company if not existing and return tbe value
            $companyName = $request->Contact['company']['name'];
            $company = $this->saveCompany($companyName);
            $companyId = $company->id;
        } //End if

        return $companyId;
    } //Function ends


    /**
    * Get All Contact Information for an Organization from Cache
    *
    * @return number
    */
    public function getAllContactData(int $orgId, int $userId=0) {
        $objReturnValue=null;
        try {
            //Get cache configuration
            $keyCache = config('portiqo-crm.settings.cache.Contacts.key').'_'.(string)$orgId;
            $durationCache = config('portiqo-crm.settings.cache.Contacts.duration_in_sec');

            if(Cache::has($keyCache)) {
                $objReturnValue = Cache::get($keyCache);
            } else {
                $objReturnValue = Cache::rememberForever($keyCache, function() use ($orgId, $userId) {
                    return $this->getAllContactDataFromDB($orgId, $userId);
                });
            } //End if
        } catch(Exception $e) {
            $objReturnValue=null;
            Log::error(json_encode($e));
        } //Try-catch ends
        
        return $objReturnValue;
    } //Function ends


    /**
    * Get All Contact Information for an Organization from DB
    *
    * @return number
    */
    public function getAllContactDataFromDB(int $orgId, int $userId=0) {
        $objReturnValue=null;
        try {
            $query = config('aqveir-class.class_model.contact.main')::with([
                'details', 'details.type','service_requests',
                'service_requests.type', 'service_requests.status', 
                'service_requests.owner']);
            $query = $query->where('org_id', $orgId);
            if($userId>0) {
                $query = $query->whereHas('service_requests', function ($inner_query) use ($userId) { 
                    $inner_query->where('owner_id', $userId);
                });
                $query = $query->whereHas('service_requests.status', function ($inner_query) { 
                    $inner_query->whereIn('value', config('portiqo-crm.settings.lookup_value.service_request_status_uber_active'));
                });
            } //End if
            $query = $query->orderBy('created_on', 'desc')->get();
            $query->makeHidden(['job_title','date_of_birth','date_of_aniversary']);
            $query->makeVisible(['created_on']);
            $query->makeVisible(['org_id']);
            
            $objReturnValue = $query;
        } catch(Exception $e) {
            $objReturnValue=null;
            Log::error(json_encode($e));
        } //Try-catch ends
        
        return $objReturnValue;
    } //Function ends


    /**
    * Get All Contact Minified Information for an Organization from DB
    *
    * @return number
    */
    public function getAllContactMinifiedDataFromDB(int $orgId) {
        $objReturnValue=null;
        try {
            $query = config('aqveir-class.class_model.contact.main')::where('org_id', $orgId);
            $query = $query->orderBy('created_on', 'desc')->get();
            $query->makeHidden(['job_title','date_of_birth','date_of_aniversary']);
            $query->makeVisible(['created_on']);
            
            $objReturnValue = $query;
        } catch(Exception $e) {
            $objReturnValue=null;
            Log::error(json_encode($e));
        } //Try-catch ends
        
        return $objReturnValue;
    } //Function ends


    /**
     * Get All Contact Information from Elastic Search
     *
     * @return number getContactRelatedData
     */
    public function getResponseDataFromElasticSearch($payload=null)
    {   
        $objReturnValue=null;
        try {
            //Create External Data Client
            $client = $this->getExternalClientForES();
            if(!$client) { throw new BadRequestHttpException(); } //End if

            //Get data from the property system
            $data = $this->getDataForV2FromES($client, $payload);
            if(!$data) { throw new BadRequestHttpException(); } //End if
            //Log::debug(json_encode($data));

            $objReturnValue=$data;
        } catch (Exception $e) {
            $objReturnValue=null;
            Log::error(json_encode($e));
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends





    /**
     *   ELASTIC SEARCH DOCUMENT DATA
     *
     *  _||_  _||_  _||_  _||_  _||_  _||_  _||_  _||_  _||_  _||_ 
     *  \  /  \  /  \  /  \  /  \  /  \  /  \  /  \  /  \  /  \  / 
     *   \/    \/    \/    \/    \/    \/    \/    \/    \/    \/  
     *
     * Get Customrs Related All Data
     */
    public function getElasticSearchDocumentData(int $orgId, int $ContactId, $request, bool $unMasskedIdentifier=false)
    {   
        $objReturnValue = null;
        try {
            // Check for Organization Id
            $orgId=1;
            if($orgId<1) {
                throw new NotFoundHttpException();
            } //End if

            //Handle client timezone if provided in request
            $clientTimeZone = null;
            if(isset($request['tz']) && $request['tz']!=null) {
                $timezone = (int) str_replace(':','',$request['tz']);
                $clientTimeZone = ((abs($timezone)==$timezone)?'+':'').trim($request['tz']);
                $request['clientTimeZone'] = $clientTimeZone;
            } else {
                $clientTimeZone = 'UTC';
                $request['clientTimeZone'] = $clientTimeZone;
            } //End if-else

            //Get Contact By Id
            if ($orgId>0 && $ContactId>0) {
                $Contact = config('aqveir-class.class_model.contact.main')::with([
                    'addresses', 'details', 'addresses.type', 'details.type', 
                    'details.country','service_requests','company'
                ]);
                $Contact = $Contact->where('org_id', $orgId);
                $Contact = $Contact->where('id', $ContactId);
                $Contact = $Contact->orderBy('created_on', 'desc')->firstOrFail();
                $Contact->makeVisible(['org_id']);
                $Contact->makeVisible(['created_on']);
            } //End if

            $servicerequests = [];
            $servicerequest = null;
            foreach($Contact['service_requests'] as $service_request) {
                
                //Get ServiceRequest Response 
                $data = $this->getFullServiceRequestFromDB($orgId, $service_request['hash'], $request, $unMasskedIdentifier);
                $data = $data->toArray();
        
                //Unset some parameters
                unset($data['properties']);
                unset($data['property']);
                unset($data['prospects']);
                 
                //Get ServiceRequestEvent By TaskEvent Ids
                $getTaskEvent = $this->getTaskandEventsDataForESDocument($orgId, $service_request['id'], ['task','calendar_event']);

                $data['taskevents'] = $getTaskEvent;
                $data['notes'] = $service_request['notes'];
                $data['communications'] = $this->getAllServiceRequestCommunication($orgId, $service_request['id']);

                array_push($servicerequests, $data);
            } //End For Loop

            //Add arrays
            $Contact = $Contact->toArray();
            $Contact['service_requests'] = $servicerequests;
            $properties['contact'] = $Contact;

            $objReturnValue = $properties;
        } catch(Exception $e) {
            Log::error(json_encode($e));
            throw new NotFoundHttpException();
        } //Try-catch ends

        return $objReturnValue;
    } //End Function


    /**
     * Get TaskEvents By Event Id
     */
    public function getTasksEvents(int $orgId, $eventId)
    {
        $objReturnValue=null;
        try{    return $eventId;
                $query = ServiceRequestEvent::with(['type', 'subtype', 'priority', 'status', 'assignee']);
                $query = $query->whereHas('type', function ($inner_query) {
                    $inner_query->where('value', config('portiqo-crm.settings.lookup_value.service_request_event_tasks'));
                });
                $query = $query->whereHas('subtype', function ($inner_query) { 
                    $inner_query->whereIn('value', config('portiqo-crm.settings.lookup_value.service_request_event_tasks_modes'));
                });
                $query = $query->where('id', $eventId);
                $query = $query->orderBy('id', 'asc')->firstOrFail();

            $objReturnValue =$query;
        } catch(Exception $e) {
            Log::error(json_encode($e));
            $objReturnValue=null;
            throw new NotFoundHttpException();
        } //Try-catch ends

        return $objReturnValue;
    } //nd Function

} //Trait ends
