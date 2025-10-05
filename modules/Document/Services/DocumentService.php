<?php

namespace Modules\Document\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Core\Repositories\Core\FileSystemRepository;
use Modules\Document\Repositories\DocumentRepository;

use Modules\Core\Services\BaseService;

use Modules\Document\Events\DocumentCreatedEvent;
use Modules\Document\Events\DocumentUpdatedEvent;
use Modules\Document\Events\DocumentDeletedEvent;

use Illuminate\Http\UploadedFile as File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Exception;
use Modules\Core\Exceptions\DuplicateDataException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class DocumentService
 * 
 * @package Modules\Document\Services
 */
class DocumentService extends BaseService
{

    /**
     * @var Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;


    /**
     * @var Modules\Core\Repositories\Lookup\LookupValueRepository
     */
    protected $lookupRepository;


    /**
     * @var Modules\Core\Repositories\Core\FileSystemRepository
     */
    protected $filesystemRepository;


    /**
     * @var Modules\Document\Repositories\DocumentRepository
     */
    protected $documentRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupRepository
     * @param \Modules\Core\Repositories\Core\FileSystemRepository              $filesystemRepository
     * @param \Modules\Document\Repositories\DocumentRepository                 $documentRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        LookupValueRepository           $lookupRepository,
        FileSystemRepository            $filesystemRepository,
        DocumentRepository              $documentRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->lookupRepository         = $lookupRepository;
        $this->filesystemRepository     = $filesystemRepository;
        $this->documentRepository       = $documentRepository;
    } //Function ends


    /**
     * Create Document
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \array $files
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function create(string $orgHash, Collection $payload, array $files, string $ipAddress=null, bool $isAutoCreated=false)
	{
		$objReturnValue=null;
		try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Lookup data
            $entityTypeId = $this->getLookupValueId($user['org_id'], $payload, 'entity_type');

            //Create
            $folderName=$orgHash . '/';
            switch ($payload['entity_type']) {
                case 'entity_type_contact':
                    $folderName .= 'contact';
                    break;

                case 'entity_type_inventory':
                    $folderName .= 'catalogue/product';
                    break;

                case 'entity_type_order':
                    $folderName .= 'order';
                    break;

                case 'entity_type_service_request':
                    $folderName .= 'lead';
                    break;

                case 'entity_type_event':
                    $folderName .= 'event';
                    break;
                
                default:
                    $folderName .= 'extra';
                    break;
            } //Switch ends
            $folderName=(empty($folderName))?:($folderName . '/' . (string) $payload['reference_id']);

            //Iterate files and upload
            $documents = [];
            foreach ($files as $file) {

                //Save the file to the storage
                $objFileStore=$this->filesystemRepository->upload($file, $folderName);
                if (empty($objFileStore)) {
                    throw new BadRequestHttpException();
                } //End if
                
                //Generate the data payload
                $data = $payload->only('reference_id', 'title', 'description')->toArray();
                $data = array_merge(
                    $data,
                    [
                        'entity_type_id'    => $entityTypeId,
                        'file_path'         => $objFileStore['file_path'],
                        'file_name'         => $objFileStore['file_name'],
                        'file_extn'         => $objFileStore['file_extn'],
                        'file_size_in_kb'   => ($objFileStore['file_size']>0)?($objFileStore['file_size']/1024):0,
                        'is_full_path'      => 0,
                        'org_id'            => $user['org_id']
                    ]
                );

                //Create Document
                $document = $this->documentRepository->create($data, $user['id'], $ipAddress);
                
                //Raise event: Document Created
                event(new DocumentCreatedEvent($document, $isAutoCreated));
                
                array_push($documents, $document);
            } //Loop ends

	        $objReturnValue = $documents;		
        } catch(AccessDeniedHttpException $e) {
            log::error('DocumentService:create:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('DocumentService:create:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('DocumentService:create:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update Document
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \string $documentHash
     * 
     * @return mixed
     */
    public function update(string $orgHash, Collection $payload, string $documentHash)
    {
		$objReturnValue=null;
		try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Build data
            $data = $payload->only(['title', 'description'])->toArray();

            //Update Document Metadata
            $document = $this->documentRepository->update($documentHash, 'hash', $data, $user['id']);

            //Raise event: Document Deleted
            event(new DocumentUpdatedEvent($document));  

	        $objReturnValue = $document;		
        } catch(AccessDeniedHttpException $e) {
            log::error('DocumentService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('DocumentService:update:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('DocumentService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends
		
		return $objReturnValue;
    } //Function ends


    /**
     * Delete Document
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \string $documentHash
     * 
     * @return mixed
     */
    public function delete(string $orgHash, Collection $payload, string $documentHash, string $ipAddress=null)
    {
		$objReturnValue=null;
		try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //SoftDelete Document
            $document = $this->documentRepository->delete($documentHash, 'hash', $user['id'], $ipAddress);
            if ($document) {
                //Raise event: Document Deleted
                event(new DocumentDeletedEvent($document));
            } //End if

	        $objReturnValue = $document;		
        } catch(AccessDeniedHttpException $e) {
            log::error('DocumentService:delete:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('DocumentService:delete:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('DocumentService:delete:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends
		
		return $objReturnValue;
    } //Function ends


    /**
     * Show/Download Document
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \string $documentHash
     * 
     * @return mixed
     */
    public function download(string $orgHash, Collection $payload, string $documentHash)
    {
		$objReturnValue=null;
		try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get Document
            $document = $this->documentRepository->getByColumn($documentHash, 'hash');

            if(empty($document)) {
                throw new BadRequestHttpException();
            } //End if

            $objReturnValue = Storage::download($document['file_path'], $document['file_name']);

        } catch(AccessDeniedHttpException $e) {
            log::error('DocumentService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('DocumentService:update:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('DocumentService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends
		
		return $objReturnValue;
    } //Function ends
   
} //Class ends