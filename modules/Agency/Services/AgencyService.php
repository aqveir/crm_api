<?php

namespace Modules\Agency\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Models\Organization\Organization;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Agency\Repositories\AgencyRepository;

use Modules\Core\Services\BaseService;

use Modules\Agency\Events\AgencyCreatedEvent;
use Modules\Agency\Events\AgencyDeletedEvent;

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
 * Class AgencyService
 * @package Modules\Agency\Services
 */
class AgencyService extends BaseService
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
     * @var \Modules\Agency\Repositories\AgencyRepository
     */
    protected $agencyRepository;


    /**
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupRepository
     * @param \Modules\Agency\Repositories\AgencyRepository                     $agencyRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        LookupValueRepository           $lookupRepository,
        AgencyRepository                $agencyRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->lookupRepository         = $lookupRepository;
        $this->agencyRepository         = $agencyRepository;
    } //Function ends


    /**
     * Create Default Agency
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \Modules\Core\Models\Organization\Organization $organization
     * 
     * @return mixed
     */
    public function createDefault(Collection $payload, Organization $organization) 
    {
        $objReturnValue=null;
        try {
            $orgHash = $organization['hash'];

            $data = null;

            $objReturnValue = $this->create($orgHash, $data, true);

        } catch(AccessDeniedHttpException $e) {
            log::error('AgencyService:createDefault:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('AgencyService:createDefault:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('AgencyService:createDefault:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Create Agency
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function create(string $orgHash, Collection $payload, bool $isAutoCreated=false)
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

            //Create Agency
            $note = $this->agencyRepository->create($data);
            $note->load('type', 'owner');
                
            //Raise event: Agency Created
            event(new NoteCreatedEvent($note, $isAutoCreated));                

            //Assign to the return value
            $objReturnValue = $note;

        } catch(AccessDeniedHttpException $e) {
            log::error('AgencyService:create:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('AgencyService:create:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('AgencyService:create:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update Agency
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \int $agencyId
     *
     * @return mixed
     */
    public function update(Collection $payload, int $agencyId)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Build data
            $data = $payload->only(['note'])->toArray();

            //Update Agency
            $note = $this->agencyRepository->update($noteId, 'id', $data, $user['id']);
                
            //Raise event: Agency Updated
            event(new NoteUpdatedEvent($note));                

            //Assign to the return value
            $objReturnValue = $note;

        } catch(AccessDeniedHttpException $e) {
            log::error('AgencyService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('AgencyService:update:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('AgencyService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Delete Agency
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \int $agencyId
     *
     * @return mixed
     */
    public function delete(Collection $payload, int $agencyId)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Get Agency
            $note = $this->agencyRepository->getById($noteId);

            //Delete Agency
            $response = $this->agencyRepository->deleteById($noteId, $user['id']);
            if ($response) {
                //Raise event: Agency Deleted
                event(new NoteDeletedEvent($note));
            } //End if
            
            //Assign to the return value
            $objReturnValue = $response;

        } catch(AccessDeniedHttpException $e) {
            log::error('AgencyService:delete:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('AgencyService:delete:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('AgencyService:delete:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends