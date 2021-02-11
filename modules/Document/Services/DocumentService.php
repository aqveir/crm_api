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

use Illuminate\Http\Request;
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
     * @param \Illuminate\Http\Request $payload
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function create(Request $request, bool $isAutoCreated=false)
	{
		$objReturnValue=null;
		try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Lookup data
            $entityType = $request['entity_type'];
            $lookupEntity = $this->lookupRepository->getLookUpByKey($user['org_id'], $entityType);
            if (empty($lookupEntity))
            {
                log::error('Unable to resolve $lookupEntity');
                throw new Exception('Unable to resolve the entity type');   
            } //End if

            //Create
            $folderName=null;
            switch ($entityType) {
                case 'entity_type_contact':
                    $folderName = 'contact';
                    break;

                case 'entity_type_inventory':
                    $folderName = 'catalogue/product';
                    break;

                case 'entity_type_order':
                    $folderName = 'order';
                    break;

                case 'entity_type_service_request':
                    $folderName = 'lead';
                    break;

                case 'entity_type_event':
                    $folderName = 'event';
                    break;
                
                default:
                    $folderName = 'extra';
                    break;
            } //Switch ends
            $folderName=(empty($folderName))?:($folderName . '/' . (string) $request['reference_id']);

            //Save the file to the storage
            $orgHash = $user->organization['hash'];
            $data=$this->filesystemRepository->upload($request, $orgHash, $folderName);
            if (empty($data)) {
                throw new BadRequestHttpException();
            } //End if

            //Generate the data payload
            $payload = $request->only('reference_id', 'title', 'description');
            $payload = array_merge(
                $payload,
                [
                    'entity_type_id'    => $lookupEntity['id'],
                    'file_path'         => $data['file_path'],
                    'file_size_in_kb'   => ($data['file_size']>0)?($data['file_size']/1024):0,
                    'is_full_path'      => 0,
                    'org_id'            => $user['org_id'],
                    'created_by'        => $user['id']
                ]
            );

            //Create Document
            $document = $this->documentRepository->create($payload);

            //Raise event: Document Created
            event(new DocumentCreatedEvent($document, $isAutoCreated));

	        $objReturnValue = $document;		
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
     * @param \Illuminate\Support\Collection $payload
     * @param \int $documentId
     * 
     * @return mixed
     */
    public function update(Collection $payload, int $documentId)
    {
		$objReturnValue=null;
		try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Build data
            $data = $payload->only(['title', 'description'])->toArray();

            //SoftDelete Document
            $document = $this->documentRepository->update($documentId, 'id', $data, $user['id']);

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
     * @param \Illuminate\Support\Collection $payload
     * @param \int $documentId
     * 
     * @return mixed
     */
    public function delete(Collection $payload, int $documentId)
    {
		$objReturnValue=null;
		try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get Document
            $document = $this->documentRepository->getById($documentId);

            //SoftDelete Document
            $response = $this->documentRepository->deleteById($documentId, $user['id']);
            if ($response) {
                //Raise event: Document Deleted
                event(new DocumentDeletedEvent($document));
            } //End if

	        $objReturnValue = $response;		
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
   
} //Class ends