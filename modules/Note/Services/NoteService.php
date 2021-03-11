<?php

namespace Modules\Note\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Note\Repositories\NoteRepository;

use Modules\Core\Services\BaseService;

use Modules\Note\Events\NoteCreatedEvent;
use Modules\Note\Events\NoteUpdatedEvent;
use Modules\Note\Events\NoteDeletedEvent;

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
 * Class NoteService
 * @package Modules\Note\Services
 */
class NoteService extends BaseService
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
     * @var \Modules\Note\Repositories\NoteRepository
     */
    protected $noteRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupRepository
     * @param \Modules\Note\Repositories\NoteRepository                         $noteRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        LookupValueRepository           $lookupRepository,
        NoteRepository                  $noteRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->lookupRepository         = $lookupRepository;
        $this->noteRepository           = $noteRepository;
    } //Function ends


    /**
     * Create Note
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function create(Collection $payload, bool $isAutoCreated=false)
    {
        $objReturnValue=null; $data=[];
        try {
            if ($isAutoCreated) {
                //Build data
                $data = $payload->only([
                    'entity_type_id', 'reference_id', 'note', 'org_id', 'created_by'
                ])->toArray();
            } else {
                //Authenticated User
                $user = $this->getCurrentUser('backend');

                //Build data
                $data = $payload->only([
                    'entity_type_id', 'reference_id', 'note'
                ])->toArray();
                $data = array_merge($data, [
                    'org_id' => $user['org_id'], 
                    'created_by' => $user['id'] 
                ]);
            } //End if

            //Lookup data
            $entityType = $payload['entity_type'];
            $lookupEntity = $this->lookupRepository->getLookUpByKey($data['org_id'], $entityType);
            if (empty($lookupEntity))
            {
                throw new Exception('Unable to resolve the entity type');   
            } //End if
            $data['entity_type_id'] = $lookupEntity['id'];

            //Create Note
            $note = $this->noteRepository->create($data);
            $note->load('type', 'owner');
                
            //Raise event: Note Created
            event(new NoteCreatedEvent($note, $isAutoCreated));                

            //Assign to the return value
            $objReturnValue = $note;

        } catch(AccessDeniedHttpException $e) {
            log::error('NoteService:create:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('NoteService:create:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('NoteService:create:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update Note
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \int $noteId
     *
     * @return mixed
     */
    public function update(Collection $payload, int $noteId)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Build data
            $data = $payload->only(['note'])->toArray();

            //Update Note
            $note = $this->noteRepository->update($noteId, 'id', $data, $user['id']);
                
            //Raise event: Note Updated
            event(new NoteUpdatedEvent($note));                

            //Assign to the return value
            $objReturnValue = $note;

        } catch(AccessDeniedHttpException $e) {
            log::error('NoteService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('NoteService:update:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('NoteService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Delete Note
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \int $noteId
     *
     * @return mixed
     */
    public function delete(Collection $payload, int $noteId)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get Note
            $note = $this->noteRepository->getById($noteId);

            //Delete Note
            $response = $this->noteRepository->deleteById($noteId, $user['id']);
            if ($response) {
                //Raise event: Note Deleted
                event(new NoteDeletedEvent($note));
            } //End if
            
            //Assign to the return value
            $objReturnValue = $response;

        } catch(AccessDeniedHttpException $e) {
            log::error('NoteService:delete:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('NoteService:delete:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('NoteService:delete:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends