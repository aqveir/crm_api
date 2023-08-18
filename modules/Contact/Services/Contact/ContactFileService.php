<?php

namespace Modules\Contact\Services\Contact;

use Config;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;

use Modules\Contact\Imports\ContactImportExcel;
use Modules\Contact\Imports\ContactImportVcard;

use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Contact\Repositories\Contact\ContactRepository;
use Modules\Contact\Repositories\Contact\ContactDetailRepository;
use Modules\Core\Repositories\Core\FileSystemRepository;

use Modules\Contact\Events\ContactUploadedEvent;
use Modules\Contact\Events\ContactBulkDataEvent;

use Modules\Core\Services\BaseService;
use Modules\Contact\Notifications\ContactImportNotification;

use Modules\Core\Traits\FileStorageAction;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile as File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;

use Exception;
use Modules\Core\Exceptions\DuplicateDataException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class ContactFileService
 * 
 * @package Modules\Contact\Services\Contact
 */
class ContactFileService extends BaseService
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
    protected $contactrepository;


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
     * @param \Modules\Contact\Repositories\Contact\ContactRepository           $contactrepository
     * @param \Modules\Contact\Repositories\Contact\ContactDetailRepository     $contactdetailRepository
     */
    public function __construct(
        OrganizationRepository              $organizationRepository,
        LookupValueRepository               $lookuprepository,
        FileSystemRepository                $filesystemRepository,
        ContactRepository                   $contactrepository,
        ContactDetailRepository             $contactdetailRepository
    ) {
        $this->organizationRepository       = $organizationRepository;
        $this->lookuprepository             = $lookuprepository;
        $this->filesystemRepository         = $filesystemRepository;
        $this->contactrepository            = $contactrepository;
        $this->contactdetailRepository      = $contactdetailRepository;
    }


    /**
     * Upload Contact
     * 
     * @param  \string  $orgHash
     * @param  \Illuminate\Support\Collection  $payload
     * @param  \array  $files
     * @param  \string  $ipAddress (optional)
     * 
     */
    public function upload(string $orgHash, Collection $payload, array $files, string $ipAddress=null)
    {
        $objReturnValue=null;

        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Upload file, if exists
            if (empty($files)) {
                throw new BadRequestHttpException();
            } //End if

            //Iterate files and upload
            $savedFiles = [];
            foreach ($files as $file) {
                //Upload file
                $savedFile = $this->uploadFile($orgHash, $file, 'contacts/bulk');

                array_push($savedFiles, $savedFile);
            } //End if

            //Notify Organization Users
            Notification::send($organization, new ContactImportNotification($organization, $savedFiles));

            //Raise upload event
            event(new ContactUploadedEvent($organization, $savedFiles, $ipAddress, $user['id']));

            //Assign to the return value
            $objReturnValue = $savedFiles;

        } catch(AccessDeniedHttpException $e) {
            log::error('ContactFileService:upload:AccessDeniedHttpException:' . $e->getMessage());
            throw $e;
        } catch(BadRequestHttpException $e) {
            log::error('ContactFileService:upload:BadRequestHttpException:' . $e->getMessage());
            throw $e;
        } catch(Exception $e) {
            log::error('ContactFileService:upload:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Process Upload Contact
     * 
     * @param  \string  $orgHash
     * @param  \Illuminate\Support\Collection  $payload
     * @param  \Illuminate\Http\UploadedFile  $files
     * @param  \string  $ipAddress (optional)
     * 
     */
    public function processUpload(string $orgHash, array $files, string $ipAddress=null, int $createdBy=0)
    {
        $objReturnValue=null;$response=[];

        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Iterate contacts
            $contacts = [];

            //Iterate the uploaded the files
            foreach ($files as $file) {
                switch ($file['file_extn']) {
                    case 'xlsx':
                        //Contact Import class
                        $contactImport = new ContactImportExcel;

                        $sheets = $contactImport->toArray(storage_path('app').'/'.$file['file_path']);
                        $response = $contactImport->processDataArray($organization, $sheets);
                        break;
                    
                    case 'csv':

                        break;

                    case 'vcf': //Virtual Card Format
                        //Contact Import class
                        $contactImport = new ContactImportVcard;
                        $vcards = $contactImport->parse(storage_path('app').'/'.$file['file_path']);
                        $response = $contactImport->processDataArray($organization, $vcards);
                        break;

                    default:
                        # code...
                        break;
                } //Switch ends

                //Add contacts to the whole collection
                $contacts = array_merge($contacts, $response);
            } //Loop ends

            /**
             * Process the contacts of data exists
             */
            if (!empty($contacts)) {
                //Validate the data
                $this->validateDuplicateData($organization, $contacts);

                //Filter data based on duplicate key
                $contactsFinal = array_filter($contacts, function($contact) {
                    return ($contact && isset($contact['duplicate']))?!$contact['duplicate']:true;
                });

                //Notification to the user
                //Notification::send($organization, new ContactImportNotification($organization, $contactsFinal));
                
                //Raise event to process bulk data
                event(new ContactBulkDataEvent($organization, $contactsFinal, $ipAddress, $createdBy));
            } //End if

        } catch(AccessDeniedHttpException $e) {
            log::error('ContactFileService:processUpload:AccessDeniedHttpException:' . $e->getMessage());
            throw $e;
        } catch(BadRequestHttpException $e) {
            log::error('ContactFileService:processUpload:BadRequestHttpException:' . $e->getMessage());
            throw $e;
        } catch(Exception $e) {
            log::error('ContactFileService:processUpload:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Validate Contacts
     * 
     * @param  \Modules\Core\Entities\Organization  $organization
     * @param  \array  $contacts
     * 
     */
    private function validateDuplicateData($organization, array &$contacts)
    {
        try {
            //Iterate contacts
            foreach ($contacts as $key => &$contact) {
                //Validate the data
                $this->validateDuplicateCheck($organization, $contact);
            } //Loop ends
        } catch(Exception $e) {
            throw $e;
        } //Try-Catch ends
    } //Function ends


    /**
     * Validate Duplicate Contacts
     * 
     * @param  \Modules\Core\Entities\Organization  $organization
     * @param  \array  $contact
     * 
     */
    private function validateDuplicateCheck($organization, array &$contact)
    {
        try {
            if (isset($contact['details']) && is_array($contact['details']) && count($contact['details'])>0) {

                //Filter data based on keys
                $details = array_filter($contact['details'], function($value) {
                    return in_array($value['type_key'], [
                        config('aqveir.settings.static.key.lookup_value.email'), 
                        config('aqveir.settings.static.key.lookup_value.phone')
                    ], false);
                });

                //Duplicate check
                $contact['duplicate'] = $this->contactdetailRepository->exits($organization['id'], $details);           
            } else { 
                $contact['duplicate'] = false;
            } //End if
        } catch(Exception $e) {
            throw $e;
        } //Try-Catch ends

        return $contact;
    } //Function ends

} //Class ends
