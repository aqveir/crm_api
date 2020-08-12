<?php

namespace Modules\Core\Services\Role;

use Config;
use Carbon\Carbon;

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
     * @param \Modules\Core\Repositories\Role\RoleRepository                $roleRepository
     * @param \Modules\Core\Repositories\Privilege\PrivilegeRepository      $privilegeRepository
     */
    public function __construct(
        RoleRepository                  $roleRepository,
        PrivilegeRepository             $privilegeRepository
    ) {
        $this->roleRepository           = $roleRepository;
        $this->privilegeRepository      = $privilegeRepository;
    } //Function ends


    /**
     * Create New Default Role for the New Organization
     *
     * @return object
     */
    public function createDefaultRole(int $orgId) {
        $objReturnValue=null;
        try {
            //Create New Elevated Role for this Organization
            $roles = config('core.settings.new_organization.default_roles');
            foreach ($roles as $role) {
                $response = $this->create(collect($role), $orgId);

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
     * @return object
     */
    public function create(Collection $request, int $orgId) {
        $objReturnValue=null;
        try {
            //Create Role for this Organization
            $data = $request->only('key', 'display_value', 'description')->toArray();
            $data['org_id'] = $orgId;
            $role = $this->roleRepository->create($data);
            if (empty($role)) {
                throw new BadRequestHttpException();
            } //End if
            
            //Assign Privileges to the Role
            $data = $request->only('privileges')->toArray();

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