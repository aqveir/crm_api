<?php

namespace App\Services\Document;

use Config;
use Carbon\Carbon;

use App\Repositories\Lookup\LookupValueRepository;
use App\Repositories\Document\DocumentRepository;
use App\Repositories\Common\FileSystemRepository;

use App\Services\BaseService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class DocumentService
 * 
 * @package App\Services\Document
 */
class DocumentService extends BaseService
{
    /**
     * @var App\Repositories\Lookup\LookupValueRepository
     */
    protected $lookuprepository;


    /**
     * @var App\Repositories\Document\DocumentRepository
     */
    protected $documentrepository;


    /**
     * @var App\Repositories\Common\FileSystemRepository
     */
    protected $filesystemrepository;


    /**
     * Document Service constructor.
     * 
     * @param \App\Repositories\Lookup\LookupValueRepository        $lookuprepository
     * @param \App\Repositories\Document\DocumentRepository         $documentrepository
     * @param \App\Repositories\Common\FileSystemRepository         $filesystemrepository
     */
    public function __construct(
        LookupValueRepository $lookuprepository,
        DocumentRepository $documentrepository,
        FileSystemRepository $filesystemrepository
    ) {
        $this->lookuprepository             = $lookuprepository;
        $this->documentrepository           = $documentrepository;
        $this->filesystemrepository         = $filesystemrepository;
    } //Function ends


    /**
     * Create Document
     * 
     * @param \Illuminate\Http\Request  $request
     * 
     * @return mixed
     */
    public function createDocument(Request $request)
	{
		$objReturnValue=null;
		try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Lookup entity
            $entityType = $request['entity_type'];
            $lookupEntity = $this->lookuprepository->getLookUpByKey($user['org_id'], $entityType);
            if (empty($lookupEntity))
            {
                log::error('Unable to resolve $lookupEntity');
                throw new Exception('Unabel to resolve the entity type');   
            } //End if

            //Create
            $folderName=null;
            switch ($entityType) {
                case 'entity_type_customer':
                    $folderName = 'customer';
                    break;

                case 'entity_type_catalogue_product':
                    $folderName = 'catalogue/product';
                    break;

                case 'entity_type_order':
                    $folderName = 'order';
                    break;
                
                default:
                    # code...
                    break;
            } //Switch ends
            $folderName=(empty($folderName))?:($folderName . '/' . (string) $request['reference_id']);

            //Save the file to the storage
            $orgHash = $user->organization['hash'];
            $pathDocument=$this->filesystemrepository->upload($request, $orgHash, $folderName);

            //Generate the data payload
            $payload = $request->only('reference_id', 'title', 'description');
            $payload = array_merge(
                $payload,
                [
                    'entity_type_id'=> $lookupEntity['id'],
                    'file_path'     => $pathDocument,
                    'is_full_path'  => 0,
                    'org_id'        => $user['org_id'],
                    'created_by'    => $user['id']
                ]
            );

            //Create Document
            $document = $this->documentrepository->create($payload);

	        $objReturnValue = $document;		
		} catch(Exception $e) {
			$objReturnValue=null;
			throw new Exception($e->getMessage());
		} //Try-catch ends
		
		return $objReturnValue;
    } //Function ends


    /**
     * Delete Document
     * 
     * @param \Illuminate\Http\Request  $request
     * @param int                       $id
     * 
     * @return mixed
     */
    public function deleteDocument(Request $request, int $id)
    {
		$objReturnValue=null;
		try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //SoftDelete Note
            $note = $this->documentrepository->deleteById($id, $user['id']);

	        $objReturnValue = $note;		
		} catch(Exception $e) {
			$objReturnValue=null;
            Log::error($e->getMessage());
            throw new Exception($e->getMessage());
		} //Try-catch ends
		
		return $objReturnValue;
    } //Function ends
   
} //Class ends