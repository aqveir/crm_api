<?php

namespace Modules\Core\Services\Organization;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;

use Modules\Core\Services\BaseService;
use Modules\Core\Services\Role\RoleService;

use Modules\Core\Events\OrganizationCreatedEvent;

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
 * Class OrganizationService
 * 
 * @package Modules\Core\Services\Organization
 */
class OrganizationService extends BaseService
{
    /**
     * @var \Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;


    /**
     * @var Modules\Core\Repositories\Lookup\LookupValueRepository
     */
    protected $lookupRepository;


    /**
     * @var \Modules\Core\Services\Role\RoleService
     */
    protected $roleService;


    /**
     * Service constructor.
     *
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupRepository
     * @param \Modules\Core\Services\Role\RoleService                           $roleService
     */
    public function __construct(
        OrganizationRepository              $organizationRepository,
        LookupValueRepository               $lookupRepository,
        RoleService                         $roleService
    ) {
        $this->organizationRepository       = $organizationRepository;
        $this->lookupRepository             = $lookupRepository;
        $this->roleService                  = $roleService;
    } //Function ends


    /**
     * Get all organization data
     * 
     * @param \Illuminate\Support\Collection $request
     * @param \bool $isActive (optional)
     *
     * @return mixed
     * 
     */
    public function getAll(Collection $request, bool $isActive=null)
    {
        $objReturnValue=null;
        try {
            $objReturnValue = $this->organizationRepository->getAllOrganizationsData();
                
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
     * Get organization data by identifier
     * 
     * @param \Illuminate\Support\Collection $request
     * @param \string $hash
     * @param \bool $isActive (optional)
     *
     * @return mixed
     * 
     */
    public function getData(Collection $request, string $hash, bool $isActive=null)
    {
        $objReturnValue=null;
        try {
            $objReturnValue = $this->organizationRepository->getOrganizationByHash($hash, $isActive);
                
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
     * Create organization
     * 
     * @param \Illuminate\Support\Collection $request
     *
     * @return mixed
     * 
     */
    public function create(Collection $request)
    {
        $objReturnValue=null;
        try {
            $keyIndustry = ($request->has('industry_type'))?$request['industry_type']:'industry_type_vanilla';
            $industry = $this->lookupRepository->getLookUpByKey(0, $keyIndustry);

            //Build Data
            $data = $request->only('name', 'subdomain', 'email', 'phone')->toArray();
            $data['industry_id'] = $industry['id'];

            //Create organization
            $organization = $this->organizationRepository->create($data);

            if ($organization) {
                //Create default role
                $roles = $this->roleService->createDefaultRole($organization['id']);

                //TODO: Create configuration data

                //Raise event: New Organization Added
                $organization['roles'] = $roles;
                event(new OrganizationCreatedEvent($organization, $request));

                //Organiztion object
                $objReturnValue = $organization;
            } //End if
                
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
     * Update organization
     * 
     * @param \Illuminate\Support\Collection $request
     * @param \string $hash
     *
     * @return mixed
     * 
     */
    public function update(Collection $request, string $hash)
    {
        $objReturnValue=null;
        try {
            $objReturnValue = $this->organizationRepository->update($request, $hash);               
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