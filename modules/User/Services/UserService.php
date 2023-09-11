<?php

namespace Modules\User\Services;

use Config;
use Carbon\Carbon;

use Modules\Core\Models\Organization\Organization;
use Modules\User\Models\User\User;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\User\Repositories\User\UserRepository;
use Modules\Core\Repositories\Lookup\LookupValueRepository;
use Modules\Core\Repositories\Role\RoleRepository;
use Modules\Core\Repositories\Privilege\PrivilegeRepository;

use Modules\User\Traits\UserAvailabilityAction;

use Modules\Core\Services\BaseService;

use Modules\User\Events\UserCreatedEvent;
use Modules\User\Events\UserUpdatedEvent;
use Modules\User\Events\UserDeletedEvent;

use Modules\User\Notifications\UserEmailVerification;
use Modules\User\Notifications\UserAccountActivation;


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
 * Class UserService
 * @package Modules\User\Services\User
 */
class UserService extends BaseService
{
    use UserAvailabilityAction;

    /**
     * @var Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;


    /**
     * @var \Modules\User\Repositories\User\UserRepository
     */
    protected $userRepository;


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
     * @param \Modules\User\Repositories\User\UserRepository                    $userRepository
     * @param \Modules\Core\Repositories\Lookup\LookupValueRepository           $lookupvalueRepository
     * @param \Modules\Core\Repositories\Role\RoleRepository                    $roleRepository
     * @param \Modules\Core\Repositories\Privilege\PrivilegeRepository          $privilegeRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        UserRepository                  $userRepository,
        LookupValueRepository           $lookupvalueRepository,
        RoleRepository                  $roleRepository,
        PrivilegeRepository             $privilegeRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->userRepository           = $userRepository;
        $this->lookupvalueRepository    = $lookupvalueRepository;
        $this->roleRepository           = $roleRepository;
        $this->privilegeRepository      = $privilegeRepository;
    } //Function ends


    /**
     * Create Default User
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \int $orgId
     * 
     * @return mixed
     */
    public function createDefault(Collection $payload, Organization $organization, string $ipAddress=null) 
    {
        $objReturnValue=null;
        try {
            $data = $payload->only(['email', 'phone', 'first_name', 'last_name'])->toArray();
            $data = array_merge($data, [
                'username' => $data['email'],
                'password' => config('user.settings.new_organization.default_password'),
                'is_remote_access_only' => 0
            ]);

            //Create defult user
            $user = $this->create($organization['hash'], collect($data), $ipAddress, true);
            if (empty($user)) {
                throw new BadRequestHttpException();
            } //End if

            //Store Additional Settings
            $user['is_active'] = true;
            $user['is_pool'] = true;
            $user['is_default'] = true;
            if (!($user->save())) {
                throw new HttpException(500);
            } //End if

            //Get Organization roles
            $roles = $organization->roles;
            if (!empty($roles)) {
                $roleOrgAdmin = collect($roles)->where('key', 'organization_admin')->first();

                //Assign the role to new user
                $user->roles()->attach($roleOrgAdmin['id'], [
                    'org_id' => $organization['id'],
                    'account_id' => null,
                    'description' => 'System Generated',
                    'created_by' => 0
                ]);
            } //End if

            //Assign to the return value
            $objReturnValue = $user;

        } catch(AccessDeniedHttpException $e) {
            log::error('UserService:createDefault:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('UserService:createDefault:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserService:createDefault:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends    


    /**
     * Create User
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \bool $isAutoCreated (optional)
     *
     * @return mixed
     */
    public function create(string $orgHash, Collection $payload, string $ipAddress=null, bool $isAutoCreated=false)
    {
        $objReturnValue = null;
        $orgId = 0; $userId = 0;

        try {
            //Authenticated User
            $userCurr = $this->getCurrentUser('backend');
            if (!empty($userCurr)) {
                if ($userCurr->hasRoles(config('aqveir.settings.default.role.key_super_admin'))) {
                    //Get organization data
                    $organization = $this->getOrganizationByHash($orgHash);
                    $orgId = $organization['id'];
                } else {
                    $organization = $userCurr->organization;
                    $orgId = $userCurr['org_id'];
                } //End if
                $userId = $userCurr['id'];
            } elseif ($isAutoCreated) { //Default creation
                //Get organization data
                $organization = $this->getOrganizationByHash($orgHash);
                $orgId = $organization['id'];
            } else {
                throw new AccessDeniedHttpException();
            } //End if

            //Build user data
            $data = $payload->only([
                'username', 'password', 
                'email', 'phone',
                'first_name', 'last_name', 
                'is_remote_access_only', 'language'
            ])->toArray();

            // Duplicate check
            $isDuplicate=$this->userRepository->exists($data['username'], 'username');
            if (!$isDuplicate) {
                //Add Organisation data
                $data = array_merge($data, [ 'org_id' => $orgId, 'created_by' => $userId ]);

                //Create User
                $user = $this->userRepository->create($data);

                //Action for non-auto users
                if (!$isAutoCreated) {
                    //Update roles
                    $this->saveRoles($orgId, $payload, $user, $userCurr);

                    //Send Verification for regular user
                    $user->notify(new UserEmailVerification());
                } //End if
                
                //Raise event: User Added
                event(new UserCreatedEvent($organization, $user, $isAutoCreated));
            } else {
                throw new BadRequestHttpException();
            } //End if

            //Assign to the return value
            $objReturnValue = $user;

        } catch(AccessDeniedHttpException $e) {
            log::error('UserService:create:AccessDeniedHttpException:' . $e->getMessage());
            throw $e;
        } catch(BadRequestHttpException $e) {
            log::error('UserService:create:BadRequestHttpException:' . $e->getMessage());
            throw $e;
        } catch(Exception $e) {
            log::error('UserService:create:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Update User
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     *
     * @return mixed
     */
    public function update(string $orgHash, Collection $payload, string $userHash, string $ipAddress=null)
    {
        $objReturnValue = null;

        try {
            //Authenticated User
            $userCurr = $this->getCurrentUser('backend');
            if (empty($userCurr)) {
                throw new AccessDeniedHttpException();
            } //End if

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);            

            //Build user data
            $data = $payload->only([
                'first_name', 'last_name', 
                'email', 'phone',
                'is_remote_access_only', 'language', 'is_active'
            ])->toArray();

            //Update User & refresh data
            $user = $this->userRepository->update($userHash, 'hash', $data, $userCurr['id']);
            if (empty($user)) {
                throw new BadRequestHttpException();
            } //End if
            $user->refresh();

            //TODO: Update pool status

            //Update roles
            $this->saveRoles($organization['id'], $payload, $user, $userCurr);

            //TODO: Update Privileges

            //Raise event: User Updated
            event(new UserUpdatedEvent($organization, $user));

            //Assign to the return value
            $objReturnValue = $user;

        } catch(AccessDeniedHttpException $e) {
            log::error('UserService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw $e;
        } catch(BadRequestHttpException $e) {
            log::error('UserService:update:BadRequestHttpException:' . $e->getMessage());
            throw $e;
        } catch(Exception $e) {
            log::error('UserService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Delete User
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     *
     * @return mixed
     */
    public function delete(string $orgHash, Collection $payload, string $userHash, string $ipAddress=null)
    {
        $objReturnValue = null;

        try {
            //Authenticated User
            $userCurr = $this->getCurrentUser('backend');
            if (empty($userCurr)) {
                throw new AccessDeniedHttpException();
            } //End if

            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);            

            //Build user data
            $data = $payload->toArray();

            //Create User
            $user = $this->userRepository->delete($userHash, 'hash', $userCurr['id']);
            if (empty($user)) {
                throw new BadRequestHttpException();
            } //End if

            //TODO: Update roles

            //TODO: Update Privileges

            //Raise event: User Updated
            event(new UserDeletedEvent($organization, $user, $ipAddress));

            //Assign to the return value
            $objReturnValue = $user;

        } catch(AccessDeniedHttpException $e) {
            log::error('UserService:update:AccessDeniedHttpException:' . $e->getMessage());
            throw $e;
        } catch(BadRequestHttpException $e) {
            log::error('UserService:update:BadRequestHttpException:' . $e->getMessage());
            throw $e;
        } catch(Exception $e) {
            log::error('UserService:update:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * User Availability Details
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \string $statusKey
     *
     * @return mixed
     */
    public function getUserByStatus(string $orgHash, Collection $payload, string $statusKey) {
        $objReturnValue = null;

        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            //Get request data
            $data = $payload->toArray();

            //Passing Params
            $statusKey = $this->getStatusKey($statusKey);
            $roleKey = $data['role'];

            //Fetch record
            $response = $this->userRepository->getRecordsByStatus($organization['id'], $statusKey, $roleKey);

            $objReturnValue = $response;
        } catch(NotFoundHttpException $e) {
            log::error('UserService:getUserByStatus:NotFoundHttpException:' . $e->getMessage());
            throw new NotFoundHttpException();
        } catch(BadRequestHttpException $e) {
            log::error('UserService:getUserByStatus:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserService:getUserByStatus:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Fetch Users for an Oraganization
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * 
     * @return mixed
     */
    public function getAll(string $orgHash, Collection $payload) {
        $objReturnValue = null;
        $orgId = 0; $userId = 0;

        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');
            if (!empty($user)) {
                if ($user->hasRoles(config('aqveir.settings.default.role.key_super_admin'))) {
                    //Get organization data
                    $organization = $this->getOrganizationByHash($orgHash);
                    $orgId = $organization['id'];
                } else {
                    $orgId = $user['org_id'];
                } //End if
                $userId = $user['id'];
            } else {
                throw new AccessDeniedHttpException();
            } //End if

            //Get request data
            $data = $payload->toArray();

            //Fetch records
            $response = $this->userRepository
                ->where('org_id', $orgId)
                ->get();

            $objReturnValue = $response;
        } catch(NotFoundHttpException $e) {
            log::error('UserService:getAll:NotFoundHttpException:' . $e->getMessage());
            throw new NotFoundHttpException();
        } catch(BadRequestHttpException $e) {
            log::error('UserService:getAll:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserService:getAll:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Fetch Single User Data for an Oraganization OR Current User Profile
     * 
     * @param  \string $orgHash
     * @param  \Illuminate\Support\Collection $payload
     * @param  \string $userHash
     * @param  \boolean $isCurrentUser (default false)
     * 
     * @return mixed
     */
    public function show(string $orgHash, Collection $payload, string $userHash=null, bool $isCurrentUser=false) {
        $objReturnValue = null;
        $orgId = 0; $userId = 0;

        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');
            if (!empty($user)) {
                if ($user->hasRoles(config('aqveir.settings.default.role.key_super_admin'))) {
                    //Get organization data
                    $organization = $this->getOrganizationByHash($orgHash);
                    $orgId = $organization['id'];
                } else {
                    $orgId = $user['org_id'];
                } //End if
                $userId = $user['id'];
            } else {
                throw new AccessDeniedHttpException();
            } //End if

            //Set the user hash in case the request is for current user
            if ($isCurrentUser) {
                $userHash = $user['hash'];
            } //End if

            //Get request data
            $data = $payload->toArray();

            //Fetch record
            $response = $this->userRepository
                ->where('hash', $userHash)
                ->where('org_id', $orgId)
                ->first();

            $objReturnValue = $response;
        } catch(NotFoundHttpException $e) {
            log::error('UserService:show:NotFoundHttpException:' . $e->getMessage());
            throw new NotFoundHttpException();
        } catch(BadRequestHttpException $e) {
            log::error('UserService:show:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserService:show:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Validate User Exists
     * 
     * @param  \string $orgHash
     * @param  \Illuminate\Support\Collection $payload
     * 
     * @return boolean
     */
    public function exists(string $orgHash, Collection $payload) {
        $objReturnValue = null;
        $orgId = 0; $userId = 0;
        $key = null;

        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            $orgId = $organization['id'];            

            //Build user data
            $data = $payload->toArray();

            if ($payload->has('username')) {
                $key='username';
            } elseif ($payload->has('email')) {
                $key='email';
            } elseif ($payload->has('phone')) {
                $key='phone';
            } else {
                $key=null;
            }

            // Duplicate check
            $isDuplicate=$this->userRepository->exists($data[$key], $key);

            //Assign to the return value
            $objReturnValue = $isDuplicate;

        } catch(AccessDeniedHttpException $e) {
            log::error('UserService:create:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('UserService:create:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserService:create:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Save User Roles
     * 
     * @param  \string $orgHash
     * @param  \Illuminate\Support\Collection $payload
     * 
     * @return boolean
     */
    private function saveRoles(int $orgId, Collection $payload, User $user, User $currUser=null) {
        $objReturnValue = null;
        $key = null;
        $actionRoles = [
            'new' => [],
            'update' => [],
            'delete' => []
        ];

        try {
            //Build user data
            $data = $payload->toArray();

            //Get roles in current request
            $requestRoles = $data['roles'];

            //Get Existing Roles
            $existingRoles = $user->roles;

            //Iterate and segragate
            foreach ($requestRoles as $requestRole) {
                $roleKey = $requestRole['key'];

                if (empty($existingRoles)) {
                    array_push($actionRoles['new'], $requestRole);
                } else {
                    $index = array_search($roleKey, array_column($existingRoles->toArray(), 'key'));
                    if ($index===FALSE) {
                        array_push($actionRoles['new'], $requestRole);
                    } else {
                        array_push($actionRoles['update'], $requestRole);
                    } //End if
                } //End if
            } //Loop ends

            //Check any deleted roles
            if (!empty($existingRoles)) {
                $existingRolesKeys = array_column($existingRoles->toArray(), 'key');
                foreach ($existingRolesKeys as $existingRolesKey) {
                    $index = array_search($existingRolesKey, array_column($requestRoles, 'key'));
                    if ($index===FALSE) {
                        $elemRole = [
                            'key' => $existingRolesKey
                        ];
                        array_push($actionRoles['delete'], $elemRole);
                    } //End if
                } //Loop ends
            } //End if

            //Iterate action array
            foreach ($actionRoles as $key => $actionRequestRoles) {
                $action=$key;

                //Iterate each action type
                foreach ($actionRequestRoles as $actionRequestRole) {
                    $accountId=null;

                    $role = $this->roleRepository
                        ->getRoleByIdForOrganization($orgId, $actionRequestRole['key']);

                    if (!empty($role)) {

                        //Get Account Id and OperatorId, if exists
                        $accountId=array_key_exists("account_id",$actionRequestRole)?$actionRequestRole['account_id']:null;
                        $operatorId=(!empty($currUser))?$currUser['id']:0;

                        //Action based on type
                        switch ($action) {
                            case 'new':
                                $user->roles()->attach($role['id'], ['account_id' => $accountId, 'created_by' => $operatorId]);
                                break;

                            case 'update':
                                $user->roles()->updateExistingPivot($role['id'], ['account_id' => $accountId, 'updated_by' => $operatorId]);
                                break;

                            case 'delete':
                                $user->roles()->detach($role['id']);
                                break;

                            default:
                                # code...
                                break;
                        } //End  switch
                    } //End if
                } //Loop ends
            } //Loop ends

            //Assign to the return value
            $objReturnValue = true;

        } catch(AccessDeniedHttpException $e) {
            log::error('UserService:saveRoles:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(BadRequestHttpException $e) {
            log::error('UserService:saveRoles:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserService:saveRoles:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends
