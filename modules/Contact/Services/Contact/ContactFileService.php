<?php

namespace Modules\Contact\Services\Contact;

use Config;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;

use Modules\Contact\Imports\ContactImport;

use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Contact\Repositories\Contact\ContactRepository;
use Modules\Contact\Repositories\Contact\ContactDetailRepository;
use Modules\Core\Repositories\Core\FileSystemRepository;

use Modules\Contact\Events\ContactUploadedEvent;

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
    protected $customerrepository;


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
     * @param \Modules\Contact\Repositories\Contact\ContactRepository           $customerrepository
     * @param \Modules\Contact\Repositories\Contact\ContactDetailRepository     $contactdetailRepository
     */
    public function __construct(
        OrganizationRepository              $organizationRepository,
        LookupValueRepository               $lookuprepository,
        FileSystemRepository                $filesystemRepository,
        ContactRepository                   $customerrepository,
        ContactDetailRepository             $contactdetailRepository
    ) {
        $this->organizationRepository       = $organizationRepository;
        $this->lookuprepository             = $lookuprepository;
        $this->filesystemRepository         = $filesystemRepository;
        $this->customerrepository           = $customerrepository;
        $this->contactdetailRepository     = $contactdetailRepository;
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
            event(new ContactUploadedEvent($organization, $savedFiles));

            //Assign to the return value
            $objReturnValue = $savedFiles;

        } catch(AccessDeniedHttpException $e) {
            log::error('ContactFileService:upload:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('ContactFileService:upload:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
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
    public function processUpload(string $orgHash, array $files, string $ipAddress=null)
    {
        $objReturnValue=null;

        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Contact Import class
            $contactImport = new ContactImport;

            //Iterate contacts
            $contacts = [];

            //Iterate the uploaded the files
            foreach ($files as $file) {
                switch ($file['file_extn']) {
                    case 'xlsx':
                        $response = $contactImport->toArray(storage_path('app').'/'.$file['file_path']);
                        $response = $this->processFileDataArray($organization, $response);
                        break;
                    
                    case 'csv':

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
                Log::info($contacts);
                Log::info('processUpload called');

                
            } //End if

            //Raise upload event
            //event(new ContactUploadedProcessesEvent($organization, 'success'));

            //Assign to the return value
            //$objReturnValue = $savedFile;

        } catch(AccessDeniedHttpException $e) {
            log::error('ContactFileService:processUpload:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('ContactFileService:processUpload:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('ContactFileService:processUpload:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Process Data Array
     * 
     * @return $contacts
     */
    private function processFileDataArray($organization, array $sheets)
    {
        try {
            //Initialize the contacts array
            $contacts = [];

            //Iterate Worksheet
            foreach ($sheets as $key => $value) {
                $rows = $value;

                //Iterate rows in the sheet
                foreach ($rows as $row) 
                {
                    //Set contact value
                    $contact = $row;

                    //Iterate columns in the row
                    foreach ($row as $key => $value) {
                        //Handle email address 
                        if ($key=='email') {
                            if (empty($contact['details'])) {
                                $contact['details'] = [];
                            } //End if                            

                            $detail = [];
                            $detail['type_key'] = 'contact_detail_type_email';
                            $detail['subtype_key'] = 'contact_detail_subtype_email_personal';
                            $detail['identifier'] = $row[$key];
                            $detail['is_primary'] = true;

                            array_push($contact['details'], $detail);
                        } //End if

                        //Handle phone number 
                        if ($key=='phone') {
                            if (empty($contact['details'])) {
                                $contact['details'] = [];
                            } //End if 

                            $detail = [];
                            $contact['details']['type_key'] = 'contact_detail_type_phone';
                            $contact['details']['subtype_key'] = 'contact_detail_subtype_email_personal';
                            $contact['details']['identifier'] = $row[$key];
                            $contact['details']['phone_idd'] = $row['phone_idd'];
                            $contact['details']['is_primary'] = true;
                        } //End if
                    } //Loop ends

                    //Validate the data
                    $this->validationDuplicateCheck($organization, $contact);

                    array_push($contacts, $contact);
                } //Loop ends
            } //Loop ends
            
            return $contacts;

        } catch(Exception $e) {
            throw $e;
        } //Try-Catch ends
    } //Function ends


    private function validationDuplicateCheck($organization, array &$row)
    {
        try {
            $dataValidate = [];

            //Iterate columns in the row
            foreach ($row as $key => $value) {
                //Verify email address
                if ($key=='email') {
                    $dataValidate[$key] = $value;
                } //End if

                //Verify phone
                if ($key=='phone') {
                    $dataValidate[$key] = $value;
                } //End if
            } //Loop ends

            //Duplicate check
            $isDuplicate=$this->contactdetailRepository->validate($organization['id'], $dataValidate);
            $row['duplicate'] = $isDuplicate;            
        } catch(Exception $e) {
            throw $e;
        } //Try-Catch ends
    } //Function ends

} //Class ends
