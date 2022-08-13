<?php

namespace Modules\Contact\Services\Contact;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Contact\Repositories\Contact\ContactRepository;
use Modules\Contact\Repositories\Contact\ContactDetailRepository;
use Modules\Core\Repositories\Core\FileSystemRepository;

use Modules\Contact\Events\ContactCreatedEvent;
use Modules\Contact\Events\ContactUpdatedEvent;
use Modules\Contact\Events\ContactDeletedEvent;
use Modules\Contact\Events\ContactUploadedEvent;

use Modules\Core\Services\BaseService;
use Modules\Contact\Notifications\ContactActivationNotification;

use Modules\Core\Traits\FileStorageAction;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile as File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use Exception;
use Modules\Core\Exceptions\DuplicateDataException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class ContactService
 * 
 * @package Modules\Contact\Services\Contact
 */
class ContactService extends BaseService
{
    use FileStorageAction;

    /**
     * @var Modules\Core\Repositories\Lookup\LookupValueRepository
     */
    protected $lookuprepository;


    /**
     * @var Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;


    /**
     * @var Modules\Contact\Repositories\Contact\ContactRepository
     */
    protected $contactRepository;


    /**
     * @var Modules\Contact\Repositories\Contact\ContactDetailRepository
     */
    protected $contactdetailRepository;


    /**
     * @var Modules\Core\Repositories\Core\FileSystemRepository
     */
    protected $filesystemRepository;

    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookuprepository
     * @param \Modules\Core\Repositories\Core\FileSystemRepository              $filesystemRepository
     * @param \Modules\Contact\Repositories\Contact\ContactRepository           $contactRepository
     * @param \Modules\Contact\Repositories\Contact\ContactDetailRepository     $contactdetailRepository
     */
    public function __construct(
        OrganizationRepository              $organizationRepository,
        LookupValueRepository               $lookuprepository,
        FileSystemRepository                $filesystemRepository,
        ContactRepository                   $contactRepository,
        ContactDetailRepository             $contactdetailRepository
    ) {
        $this->organizationRepository       = $organizationRepository;
        $this->lookuprepository             = $lookuprepository;
        $this->filesystemRepository         = $filesystemRepository;
        $this->contactRepository            = $contactRepository;
        $this->contactdetailRepository      = $contactdetailRepository;
    } //Function ends


    /**
     * Contact Exists
     * 
     * @param string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * 
     * @return bool
     */
    public function exists(string $orgHash, Collection $payload)
    {
        $objReturnValue=false;
        try {
            //Get organization data
            $organization = $this->organizationRepository->getOrganizationByHash($orgHash);

            $type_key = null;
            $data = null;
            if ($payload->has('email')) {
                $type_key = config('omnichannel.settings.static.key.lookup_value.email');
                $data = $payload['email'];
            } elseif ($payload->has('phone')) {
                $type_key = config('omnichannel.settings.static.key.lookup_value.phone');
                $data = $payload['phone'];
            } else {
                throw new Exception();
            } //End if

            //Check if the Contact exists
            $response = $this->contactdetailRepository->getContactDetailsByIdentifier($organization['id'], $data, null, true, true);

            $objReturnValue = !empty($response);
        } catch(ModelNotFoundException $e) {
            Log::error('ContactService:exists:ModelNotFoundException:' . $e->getMessage());
        } catch (Exception $e) {
            log::error('ContactService:exists:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Get All Contact Full Data (Backend)
     * 
     * @param \Illuminate\Support\Collection $payload
     * 
     * @return object
     */
    public function index(string $orgHash, Collection $payload)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Forced params
            $isForcedFromDB = $this->isForced($payload);

            //Page number and size limit
            $page = ($payload->has('page'))?$payload['page']:1;
            $size = ($payload->has('size'))?$payload['size']:10;

            //Load Contact Data
            $objReturnValue = $this->contactRepository
                ->getFullData($user['org_id'], $orgHash, $isForcedFromDB, $page, $size);

        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Get Contact Full Data By Hash Identifier (Backend)
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param string $hash (Customer Hash)
     * 
     * @return object
     */
    public function show(string $orgHash, Collection $payload, string $hash)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Forced params
            $isForcedFromDB = true; //$this->isForced($payload);

            //Load Contact Data
            $objReturnValue = $this->contactRepository->getFullDataByIdentifier($user['org_id'], $hash, $isForcedFromDB);

        } catch(Exception $e) {
            log::error($e);
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Get Contact Full Data By Hash Identifier (Frontend)
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return object
     */
    public function getContactFullData(string $orgHash, Request $request)
    {
        $objReturnValue=null;
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if(empty($organization)) {
                throw new AccessDeniedHttpException();
            } //End if

            //Authenticated User
            $contact = $this->getCurrentUser('frontend');

            log::info(json_encode($contact).'->'. $organization['id'].'->'.$contact['hash']);

            //Load Contact Data
            $objReturnValue = $this->contactRepository->getFullDataFromDB($organization['id'], $contact['hash']);

        } catch(AccessDeniedHttpException $e) {
            throw new AccessDeniedHttpException();
        } catch(UnauthorizedHttpException $e) {
            throw new UnauthorizedHttpException();
        } catch(Exception $e) {
            throw new Exception($e);
        } //Try-catch ends
        return $objReturnValue;
    } //Function ends


    /**
     * Function to return the phone number of a contact
     *
     * @return objReturnValue
     */
    public function getContactPhone(int $orgId=0, $contact, string $proxy=null, $isPrimary=null)
    {
        $objReturnValue = null;
        try {
            //Get Phone Type for the Organization
            $type = $this->lookuprepository->getLookUpByKey($orgId, config('omnichannel.settings.static.key.lookup_value.phone'));

            //Get phone details for a contact
            $isPrimary = ($proxy!=null)?null:true;
            $contactDetail = $this->contactdetailRepository->getContactDetailsByType($contact['id'], $type['id'], $isPrimary, $proxy);

            if($contactDetail!=null) {
                $country_code = '91'; //$contactDetail['country']['code'];
                $phone_number = $contactDetail['identifier'];
                $objReturnValue = '+'.$country_code.$phone_number;
            } else { throw new BadRequestHttpException(); } //End if-else
            
        } catch(Exception $e) {
            Log::error(json_encode($e));
            throw new NotFoundHttpException();
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Create Default User
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \int $orgId
     * 
     * @return mixed
     */
    public function createDefault(Collection $payload, Organization $organization, string $ipAddress=null)
    {
        $objReturnValue=null;
        try {
            $data = $payload->only(['email', 'phone', 'first_name', 'last_name'])->toArray();
            $data = array_merge($data, [
                'username' => $data['email'],
                'password' => config('user.settings.new_organization.default_password'),
                'is_remote_access_only' => 0
            ]);

            //Create defult user
            $user = $this->create($organization['hash'], collect($data), $ipAddress, true);
            if (empty($user)) {
                throw new BadRequestHttpException();
            } //End if

            //Store Additional Settings
            $user['is_active'] = true;
            $user['is_pool'] = true;
            $user['is_default'] = true;
            if (!($user->save())) {
                throw new HttpException(500);
            } //End if

            //Get Organization roles
            $roles = $organization->roles;
            if (!empty($roles)) {
                $roleOrgAdmin = collect($roles)->where('key', 'organization_admin')->first();

                //Assign the role to new user
                $user->roles()->attach($roleOrgAdmin['id'], [
                    'account_id' => null,
                    'description' => 'System Generated',
                    'created_by' => 0
                ]);
            } //End if

            //Assign to the return value
            $objReturnValue = $user;

        } catch(AccessDeniedHttpException $e) {
            log::error('UserService:createDefault:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('UserService:createDefault:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserService:createDefault:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends 


    /**
     * Create Contact
     * 
     * @param  \string  $orgHash
     * @param  \Illuminate\Support\Collection  $payload
     * @param  \string  $ipAddress (optional)
     * @param  \bool  $isAutoCreated (optional)
     * 
     */
    public function create(string $orgHash, Collection $payload, string $ipAddress=null, bool $isAutoCreated=false)
    {
        $objReturnValue=null;
        $payloadDetails=[];

        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            $createdBy = 0;
            if (!$isAutoCreated) {
                $user = $this->getCurrentUser('backend');
                $createdBy = $user['id'];
            } //End if

            //Data for validation
            $dataValidate = ($payload->only(['email', 'phone']))->toArray();

            //Duplicate check
            $isDuplicate=$this->contactdetailRepository->validate($organization['id'], $dataValidate);
            if (!$isDuplicate) {

                //Generate the data payload to create user
                $payloadContact = $payload->only('password', 'first_name', 'middle_name', 'last_name')->toArray();
                $payloadContact = array_merge(
                    $payloadContact,
                    [
                        'org_id' => $organization['id'],
                        '2fa_secret' => null,
                        'group_id' => 0,
                    ]
                );

                //Create Contact
                $contact = $this->contactRepository->create($payloadContact, $createdBy, $ipAddress);

                //Create Contact details - Email
                if(!empty($payload['email'])) {
                    //Get Email Type for the Organization
                    $type = $this->getLookupValueByKey($organization['id'], config('aqveir.settings.static.key.lookup_value.email'));

                    array_push($payloadDetails, [
                        'org_id' => $organization['id'],
                        'type_id' => (empty($type)?0:$type['id']),
                        'contact_id' => $contact['id'],
                        'identifier' => $payload['email'],
                        'is_primary' => 1,
                        'is_verified' => $isEmailValid,
                        'created_by'=> $createdBy
                    ]);
                } //End if

                //Create Contact details - Phone
                if(!empty($payload['phone'])) {
                    //Get Phone Type for the Organization
                    $type = $this->getLookupValueByKey($organization['id'], config('aqveir.settings.static.key.lookup_value.phone'));

                    array_push($payloadDetails, [
                        'org_id' => $organization['id'],
                        'type_id' => (empty($type)?0:$type['id']),
                        'contact_id' => $contact['id'],
                        'identifier' => $payload['phone'],
                        'is_primary' => 1,
                        'is_verified' => $isPhoneValid,
                        'created_by'=> $createdBy
                    ]);

                    //$this->contactdetailRepository->create($payloadDetails);
                } //End if

                //Notify user to Activate Account
                //$contact->notify(new ContactActivationNotification($contact));

                //Raise event: New Contact Added
                event(new ContactCreatedEvent($contact));

                $objReturnValue=$contact;
            } else {
                throw new DuplicateDataException();
            } //End if
        } catch(DuplicateDataException $e) {
            throw $e;
        } catch(Exception $e) {
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update Contact
     * 
     * @param  \string  $orgHash
     * @param  \Illuminate\Support\Collection  $payload
     * @param  \string  $cHash
     * @param  \string  $ipAddress (optional)
     * 
     */
    public function update(string $orgHash, Collection $payload, string $cHash, string $ipAddress=null)
    {
        $objReturnValue=null;

        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            $createdBy = 0;
            if (!$isAutoCreated) {
                $user = $this->getCurrentUser('backend');
                $createdBy = $user['id'];
            } //End if

            //Data for validation
            $dataValidate = ($payload->only(['email', 'phone']))->toArray();

            //Duplicate check
            $isDuplicate=$this->contactdetailRepository->validate($organization['id'], $dataValidate);
            if (!$isDuplicate) {

                //Generate the data payload to create user
                $payload = $payload->only('password', 'first_name', 'middle_name', 'last_name');
                $payload = array_merge(
                    $payload,
                    [
                        'org_id' => $organization['id'],
                        '2fa_secret' => null,
                        'group_id' => 0,
                    ]
                );

                //Create Contact
                $contact = $this->contactRepository->create($payload, $createdBy, $ipAddress);

                //Create Contact details - Email
                if(!empty($payload['email'])) {
                    //Get Email Type for the Organization
                    $type = $this->getLookupValueByKey($organization['id'], config('omnichannel.settings.static.key.lookup_value.email'));

                    $payloadDetails = [
                        'org_id' => $organization['id'],
                        'type_id' => (empty($type)?0:$type['id']),
                        'contact_id' => $contact['id'],
                        'identifier' => $payload['email'],
                        'is_primary' => 1,
                        'is_verified' => $isEmailValid,
                        'created_by'=> $createdBy
                    ];
                    $this->contactdetailRepository->create($payloadDetails);
                } //End if

                //Create Contact details - Phone
                if(!empty($payload['phone'])) {
                    //Get Phone Type for the Organization
                    $type = $this->getLookupValueByKey($organization['id'], config('omnichannel.settings.static.key.lookup_value.phone'));

                    $payloadDetails = [
                        'org_id' => $organization['id'],
                        'type_id' => (empty($type)?0:$type['id']),
                        'contact_id' => $contact['id'],
                        'identifier' => $payload['phone'],
                        'is_primary' => 1,
                        'is_verified' => $isPhoneValid,
                        'created_by'=> $createdBy
                    ];
                    $this->contactdetailRepository->create($payloadDetails);
                } //End if

                //Notify user to Activate Account
                //$contact->notify(new ContactActivationNotification($contact));

                //Raise event: New Contact Added
                event(new ContactCreatedEvent($contact));

                $objReturnValue=$contact;
            } else {
                throw new DuplicateDataException();
            } //End if
        } catch(DuplicateDataException $e) {
            throw new DuplicateDataException();
        } catch(Exception $e) {
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Delete Contact
     * 
     * @param  \string  $orgHash
     * @param  \Illuminate\Support\Collection  $payload
     * @param  \string  $cHash
     * @param  \string  $ipAddress (optional)
     * 
     */
    public function delete(string $orgHash, Collection $payload, string $ipAddress=null)
    {
        $objReturnValue=null;

        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            $createdBy = 0;
            if (!$isAutoCreated) {
                $user = $this->getCurrentUser('backend');
                $createdBy = $user['id'];
            } //End if

            //Data for validation
            $dataValidate = ($payload->only(['email', 'phone']))->toArray();

            //Duplicate check
            $isDuplicate=$this->contactdetailRepository->validate($organization['id'], $dataValidate);
            if (!$isDuplicate) {

                //Generate the data payload to create user
                $payload = $payload->only('password', 'first_name', 'middle_name', 'last_name');
                $payload = array_merge(
                    $payload,
                    [
                        'org_id' => $organization['id'],
                        '2fa_secret' => null,
                        'group_id' => 0,
                    ]
                );

                //Create Contact
                $contact = $this->contactRepository->create($payload, $createdBy, $ipAddress);

                //Create Contact details - Email
                if(!empty($payload['email'])) {
                    //Get Email Type for the Organization
                    $type = $this->getLookupValueByKey($organization['id'], config('omnichannel.settings.static.key.lookup_value.email'));

                    $payloadDetails = [
                        'org_id' => $organization['id'],
                        'type_id' => (empty($type)?0:$type['id']),
                        'contact_id' => $contact['id'],
                        'identifier' => $payload['email'],
                        'is_primary' => 1,
                        'is_verified' => $isEmailValid,
                        'created_by'=> $createdBy
                    ];
                    $this->contactdetailRepository->create($payloadDetails);
                } //End if

                //Create Contact details - Phone
                if(!empty($payload['phone'])) {
                    //Get Phone Type for the Organization
                    $type = $this->getLookupValueByKey($organization['id'], config('omnichannel.settings.static.key.lookup_value.phone'));

                    $payloadDetails = [
                        'org_id' => $organization['id'],
                        'type_id' => (empty($type)?0:$type['id']),
                        'contact_id' => $contact['id'],
                        'identifier' => $payload['phone'],
                        'is_primary' => 1,
                        'is_verified' => $isPhoneValid,
                        'created_by'=> $createdBy
                    ];
                    $this->contactdetailRepository->create($payloadDetails);
                } //End if

                //Notify user to Activate Account
                //$contact->notify(new ContactActivationNotification($contact));

                //Raise event: New Contact Added
                event(new ContactCreatedEvent($contact));

                $objReturnValue=$contact;
            } else {
                throw new DuplicateDataException();
            } //End if
        } catch(DuplicateDataException $e) {
            throw new DuplicateDataException();
        } catch(Exception $e) {
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update Avatar Contact
     * 
     * @param  \string  $orgHash
     * @param  \Illuminate\Support\Collection  $payload
     * @param  \string  $cHash
     * @param  \File  $file
     * @param  \string  $ipAddress (optional)
     * 
     */
    public function updateAvatar(string $orgHash, Collection $payload, string $cHash, File $file=null, string $ipAddress=null)
    {
        $objReturnValue=null;

        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Upload Logo, if exists
            $data = [];
            if (!empty($file)) {
                $avatar = $this->uploadImage($organization['hash'], $file, 'contact/'.$cHash.'/avatar');
                $data['avatar'] = $avatar['file_path'];

                //Update contact
                $contact = $this->contactRepository->update($cHash, 'hash', $data, $user['id'], $ipAddress);
                if ($contact) {
                    //Raise event: Contact Updated
                    event(new ContactUpdatedEvent($contact));
                } //End if
            } //End if

            $objReturnValue=$contact;
        } catch(DuplicateDataException $e) {
            throw new DuplicateDataException();
        } catch(Exception $e) {
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends
