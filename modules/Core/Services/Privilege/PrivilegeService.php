<?php

namespace Modules\Core\Services\Privilege;

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
 * Class PrivilegeService
 * 
 * @package Modules\Core\Services\Role
 */
class PrivilegeService extends BaseService
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
     * @var \Modules\Core\Repositories\Privilege\PrivilegeRepository
     */
    protected $privilegeRepository;


    /**
     * Service constructor.
     *
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupvalueRepository
     * @param \Modules\Core\Repositories\Privilege\PrivilegeRepository          $privilegeRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        LookupValueRepository           $lookupvalueRepository,
        PrivilegeRepository             $privilegeRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->lookupvalueRepository    = $lookupvalueRepository;
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

            $response = $this->privilegeRepository
                ->where('is_secure', false)
                ->get();

            //Return the response data
            $objReturnValue = $response;            

        } catch(AccessDeniedHttpException $e) {
            log::error('PrivilegeService:index:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('PrivilegeService:index:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('PrivilegeService:index:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Get Data by Privilege Key
     *
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \string $key
     *
     * @return mixed
     */
    public function show(string $orgHash, Collection $payload, string $key) {
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

            $response = $this->privilegeRepository
                ->where('key', $key)
                ->first();

            if (empty($response)) {
                throw new NotFoundHttpException();
            } //End if

            //Return the response data
            $objReturnValue = $response;            

        } catch(AccessDeniedHttpException $e) {
            log::error('PrivilegeService:show:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(NotFoundHttpException $e) {
            log::error('PrivilegeService:show:NotFoundHttpException:' . $e->getMessage());
            throw new NotFoundHttpException();
        } catch(BadRequestHttpException $e) {
            log::error('PrivilegeService:show:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('PrivilegeService:show:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Create Privileges for the Application
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
            $data = $payload->only('key', 'display_value', 'description', 'is_secure')->toArray();
            $privilege = $this->privilegeRepository->create($data);
            if (empty($privilege)) {
                throw new BadRequestHttpException();
            } //End if
            
            //Return the Newly Created Privilege
            $objReturnValue = $privilege;            

        } catch(AccessDeniedHttpException $e) {
            log::error('PrivilegeService:create:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('PrivilegeService:create:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('PrivilegeService:create:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update Privileges for the Application
     *
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \string $key
     * 
     * @return mixed
     */
    public function update(string $orgHash, Collection $payload, string $key) {
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
            $data = $payload->only('display_value', 'description', 'is_active', 'is_secure')->toArray();
            $privilege = $this->privilegeRepository->update($key, 'key', $data);
            if (empty($privilege)) {
                throw new BadRequestHttpException();
            } //End if
            
            //Return the Newly Created Role
            $objReturnValue = $privilege;            

        } catch(AccessDeniedHttpException $e) {
            log::error('PrivilegeService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('PrivilegeService:update:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('PrivilegeService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Delete Privileges for the Application
     *
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \string $key
     * 
     * @return mixed
     */
    public function delete(string $orgHash, Collection $payload, string $key) {
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
            $privilege = $this->privilegeRepository->update($key, 'key', $data);
            if (empty($privilege)) {
                throw new BadRequestHttpException();
            } //End if
            
            //Return the Newly Created Role
            $objReturnValue = $privilege;            

        } catch(AccessDeniedHttpException $e) {
            log::error('PrivilegeService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('PrivilegeService:update:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('PrivilegeService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends