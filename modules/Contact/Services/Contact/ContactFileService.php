<?php

namespace Modules\Contact\Services\Contact;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Contact\Repositories\Contact\ContactRepository;
use Modules\Contact\Repositories\Contact\ContactDetailRepository;
use Modules\Core\Repositories\Core\FileSystemRepository;

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
    protected $customerdetailrepository;


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
     * @param \Modules\Contact\Repositories\Contact\ContactDetailRepository     $customerdetailrepository
     */
    public function __construct(
        OrganizationRepository              $organizationRepository,
        LookupValueRepository               $lookuprepository,
        FileSystemRepository                $filesystemRepository,
        ContactRepository                   $customerrepository,
        ContactDetailRepository             $customerdetailrepository
    ) {
        $this->organizationRepository       = $organizationRepository;
        $this->lookuprepository             = $lookuprepository;
        $this->filesystemRepository         = $filesystemRepository;
        $this->customerrepository           = $customerrepository;
        $this->customerdetailrepository     = $customerdetailrepository;
    }


    /**
     * Upload Contact
     * 
     * @param  \string  $orgHash
     * @param  \Illuminate\Support\Collection  $payload
     * @param  \Illuminate\Http\UploadedFile  $files
     * @param  \string  $ipAddress (optional)
     * 
     */
    public function upload(string $orgHash, Collection $payload, array $files, string $ipAddress=null)
    {
        $objReturnValue=null;

        try {
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

            //Raise upload event
            //event(new ContactUploadedEvent($organization, $savedFile));

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
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  \string  $ipAddress (optional)
     * 
     */
    public function processUpload(string $orgHash, Collection $payload, string $ipAddress=null)
    {
        $objReturnValue=null;

        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Create file object from path
            //$file = new File($payload['file_path']);

            var_dump($payload);


            /**
             * REFER: https://www.youtube.com/watch?v=6P_nqOX38CE
             */


            Log::info('processUpload called');


            //Raise upload event
            //event(new ContactUploadedProcessesEvent($organization, 'success'));

            //Assign to the return value
            //$objReturnValue = $savedFile;

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

} //Class ends
