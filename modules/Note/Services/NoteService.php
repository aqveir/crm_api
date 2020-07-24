<?php

namespace Modules\Note\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Note\Repositories\Note\NoteRepository;

use Modules\Core\Services\BaseService;

use Modules\Note\Events\NoteCreatedEvent;

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
     * @var \Modules\Note\Repositories\Note\NoteRepository
     */
    protected $noteRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\Note\Repositories\Note\NoteRepository    $noteRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        NoteRepository                  $noteRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->noteRepository           = $noteRepository;
    } //Function ends


    /**
     * Create User
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function create(Collection $payload, bool $isAutoCreated=false)
    {
        $objReturnValue=null;
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

            //Create Note
            $note = $this->noteRepository->create($data);   
                
            //Raise event: Note Added
            event(new NoteCreatedEvent($note, $isAutoCreated));                

            //Assign to the return value
            $objReturnValue = $note;

        } catch(AccessDeniedHttpException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            Log::error($e);
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

            //Create Note
            $note = $this->noteRepository->update($data);   
                
            //Raise event: Note Updated
            event(new NoteUpdatedEvent($note));                

            //Assign to the return value
            $objReturnValue = $note;

        } catch(AccessDeniedHttpException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            Log::error($e);
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

            //Build data
            $data = $payload->only([
                'entity_type_id', 'reference_id', 'note'
            ])->toArray();
            $data = array_merge($data, [
                'org_id' => $user['org_id'],
                'created_by' => $user['id']
            ]);

            //Create Note
            $note = $this->noteRepository->delete($data);   
                
            //Raise event: Note Deleted
            event(new NoteDeletedEvent($note, $isAutoCreated));                

            //Assign to the return value
            $objReturnValue = $note;

        } catch(AccessDeniedHttpException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            Log::error($e);
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends