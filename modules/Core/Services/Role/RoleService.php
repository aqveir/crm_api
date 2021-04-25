<?php

namespace Modules\Core\Services\Role;

use Config;
use Carbon\Carbon;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Core\Repositories\Role\RoleRepository;
use Modules\Core\Repositories\Privilege\PrivilegeRepository;

use Modules\Core\Services\BaseService;

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
 * Class RoleService
 * 
 * @package Modules\Core\Services\Role
 */
class RoleService extends BaseService
{
    /**
     * @var Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;


    /**
     * @var \Modules\Core\Repositories\Lookup\LookupValueRepository
     */
    protected $lookupvalueRepository;


    /**
     * @var \Modules\Core\Repositories\Role\RoleRepository
     */
    protected $roleRepository;


    /**
     * @var \Modules\Core\Repositories\Privilege\PrivilegeRepository
     */
    protected $privilegeRepository;


    /**
     * Service constructor.
     *
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupvalueRepository
     * @param \Modules\Core\Repositories\Role\RoleRepository                    $roleRepository
     * @param \Modules\Core\Repositories\Privilege\PrivilegeRepository          $privilegeRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        LookupValueRepository           $lookupvalueRepository,
        RoleRepository                  $roleRepository,
        PrivilegeRepository             $privilegeRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->lookupvalueRepository    = $lookupvalueRepository;
        $this->roleRepository           = $roleRepository;
        $this->privilegeRepository      = $privilegeRepository;
    } //Function ends


    /**
     * Get Collection of All Data
     *
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     *
     * @return mixed
     */
    public function index(string $orgHash, Collection $payload) {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');
            if (empty($user)) { 
                throw new AccessDeniedHttpException();
            } //End if

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Get request data
            $data = $payload->toArray();

            //Get roles for the organization
            $response = $this->roleRepository
                ->getRolesForOrganization($organization['id']);

            //Return the response data
            $objReturnValue = $response;            

        } catch(AccessDeniedHttpException $e) {
            log::error('RoleService:index:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('RoleService:index:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('RoleService:index:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Get Data by Lookup Key
     *
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function show(string $orgHash, Collection $payload, string $roleKey) {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');
            if (empty($user)) { 
                throw new AccessDeniedHttpException();
            } //End if

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Get request data
            $data = $payload->toArray();

            $response = $this->roleRepository
                ->getRoleByIdForOrganization($organization['id'], $roleKey);

            if (empty($response)) {
                throw new NotFoundHttpException();
            } //End if

            //Return the response data
            $objReturnValue = $response;            

        } catch(AccessDeniedHttpException $e) {
            log::error('RoleService:show:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(NotFoundHttpException $e) {
            log::error('RoleService:show:NotFoundHttpException:' . $e->getMessage());
            throw new NotFoundHttpException();
        } catch(BadRequestHttpException $e) {
            log::error('RoleService:show:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('RoleService:show:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Create New Default Role for the New Organization
     * 
     * @param \string $orgHash
     *
     * @return mixed
     */
    public function createDefaultRole(string $orgHash) {
        $objReturnValue=null;
        try {
            //Create New Elevated Role for this Organization
            $roles = config('core.settings.new_organization.default_roles');
            foreach ($roles as $role) {
                $response = $this->create($orgHash, collect($role));

                //Add response to array
                if (!empty($response)) {
                    $objReturnValue = (empty($objReturnValue))?[]:$objReturnValue;
                    array_push($objReturnValue, $response);
                } //End if

            } //Loop ends
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
     * Create Role for the Organization
     *
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * 
     * @return mixed
     */
    public function create(string $orgHash, Collection $payload) {
        $objReturnValue=null;
        try {
            if (!empty($orgHash)) {
                //Authenticated User
                $user = $this->getCurrentUser('backend');
                if (empty($user)) { 
                    throw new AccessDeniedHttpException();
                } //End if

                //Get organization data
                $organization = $this->getOrganizationByHash($orgHash);
                $orgId = $organization['id'];
            } //End if

            //Create Role for this Organization
            $data = $payload->only('key', 'display_value', 'description')->toArray();
            $data['org_id'] = $orgId;
            $role = $this->roleRepository->create($data);
            if (empty($role)) {
                throw new BadRequestHttpException();
            } //End if
            
            //Assign Privileges to the Role
            $data = $payload->only('privileges')->toArray();

            //Get all active privileges
            $privileges = $this->privilegeRepository->getPrivilegesByNames($data['privileges'], 'key', true);

            //Get all privileges id into an array
            $privilegesId = null;
            if (!empty($privileges)) {
                $privilegesId=[];
                foreach ($privileges as $privilege) {
                    array_push($privilegesId, $privilege['id']);
                } //Loop ends

                //Attach the privileges to the role
                $role->privileges()->attach($privilegesId);                
            } //End if

            //Return the Newly Created Role
            $objReturnValue = $role;            

        } catch(AccessDeniedHttpException $e) {
            log::error('RoleService:create:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('RoleService:create:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('RoleService:create:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update Role for the Organization
     *
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \string $roleKey
     * 
     * @return mixed
     */
    public function update(string $orgHash, Collection $payload, string $roleKey) {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');
            if (empty($user)) { 
                throw new AccessDeniedHttpException();
            } //End if

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Create Role for this Organization
            $data = $payload->only('display_value', 'description', 'is_active')->toArray();
            $role = $this->roleRepository->update($roleKey, 'key', $data);
            if (empty($role)) {
                throw new BadRequestHttpException();
            } //End if
            
            //Assign Privileges to the Role
            $data = $payload->only('privileges')->toArray();

            //Get all active privileges
            $privileges = $this->privilegeRepository->getPrivilegesByNames($data['privileges'], 'key', true);

            //Get all privileges id into an array
            $privilegesId = null;
            if (!empty($privileges)) {
                $privilegesId=[];
                foreach ($privileges as $privilege) {
                    array_push($privilegesId, $privilege['id']);
                } //Loop ends

                //Attach the privileges to the role
                $role->privileges()->sync($privilegesId);                
            } //End if

            //Return the Newly Created Role
            $objReturnValue = $role;            

        } catch(AccessDeniedHttpException $e) {
            log::error('RoleService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('RoleService:update:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('RoleService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Delete Role for the Organization
     *
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \string $roleKey
     * 
     * @return mixed
     */
    public function delete(string $orgHash, Collection $payload, string $roleKey) {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');
            if (empty($user)) { 
                throw new AccessDeniedHttpException();
            } //End if

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Mark Role as in-active
            $data = ['is_active' => false];
            $role = $this->roleRepository->update($roleKey, 'key', $data);
            if (empty($role)) {
                throw new BadRequestHttpException();
            } //End if

            //Detach the privileges from the role
            //$role->privileges()->detach(); 
            
            //Return the Newly Created Role
            $objReturnValue = $role;            

        } catch(AccessDeniedHttpException $e) {
            log::error('RoleService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('RoleService:update:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('RoleService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends