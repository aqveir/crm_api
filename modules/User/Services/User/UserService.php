<?php

namespace Modules\User\Services\User;

use Config;
use Carbon\Carbon;

use Modules\Core\Models\Organization\Organization;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\User\Repositories\User\UserRepository;
use Modules\User\Traits\UserAvailabilityAction;

use Modules\Core\Services\BaseService;

use Modules\User\Events\UserCreatedEvent;

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
     * Service constructor.
     * 
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Modules\User\Repositories\User\UserRepository                    $userRepository
     */
    public function __construct(
        OrganizationRepository          $organizationRepository,
        UserRepository                  $userRepository
    ) {
        $this->organizationRepository   = $organizationRepository;
        $this->userRepository           = $userRepository;
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
            $user = $this->getCurrentUser('backend');
            if (!empty($user)) {
                if ($user->hasRoles(config('crmomni.settings.default.role.key_super_admin'))) {
                    //Get organization data
                    $organization = $this->getOrganizationByHash($orgHash);
                    $orgId = $organization['id'];
                } else {
                    $orgId = $user['org_id'];
                } //End if
                $userId = $user['id'];
            } elseif ($isAutoCreated) { //Default creation
                //Get organization data
                $organization = $this->getOrganizationByHash($orgHash);
                $orgId = $organization['id'];
            } else {
                throw new AccessDeniedHttpException();
            } //End if

            //Build user data
            $data = $payload->only([
                'username', 'password', 'email', 'phone', 
                'first_name', 'last_name', 'is_remote_access_only'
            ])->toArray();

            // Duplicate check
            $isDuplicate=$this->userRepository->exists($data['username'], 'username');
            if (!$isDuplicate) {
                //Add Organisation data
                $data = array_merge($data, [ 'org_id' => $orgId, 'created_by' => $userId, 'verification_token' => 'null' ]);

                //Create User
                $user = $this->userRepository->create($data);

                //Send Verification for regular user
                if (!$isAutoCreated) {
                    $user->notify(new UserEmailVerification());
                } //End if
                
                //Raise event: User Added
                event(new UserCreatedEvent($user, $isAutoCreated));
            } else {
                throw new BadRequestHttpException();
            } //End if

            //Assign to the return value
            $objReturnValue = $user;

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
     * Verify User Email
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $payload
     * @param \string $token
     *
     * @return mixed
     */
    public function verify(string $orgHash, Collection $payload, string $token) {
        $objReturnValue = null;

        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);

            $data = $payload->toArray();

            //Get User data
            $user = $this->userRepository
                ->where('org_id', $organization['id'])
                ->where('email', $data['email'])
                ->where('verification_token', $token)
                ->where('is_verified', false)
                ->where('is_active', true)
                ->firstOrFail();

            //Check if the request is valid
            if (!empty($user)) {
                $user['is_verified'] = true;
                $user['verified_at'] = Carbon::now();
                $user->save();
            } else {
                throw new BadRequestHttpException();
            } //End if

            //Assign to the return value
            $objReturnValue = $user;

        } catch(BadRequestHttpException $e) {
            log::error('UserService:register:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserService:register:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Activate User Account
     * 
     * @param \Illuminate\Support\Collection $payload
     * @param \string $token
     *
     * @return mixed
     */
    public function activate(Collection $payload, string $token) {
        $objReturnValue = null;
        $orgId = 0; $userId = 0;

        try {
            //Build user data
            $data = $payload->only([
                'first_name', 'last_name',
                'email', 'phone',
            ])->toArray();

            // Duplicate check
            $isDuplicate=$this->userRepository->exists($data['email'], 'email');
            if (!$isDuplicate) {
                //Create User
                $user = $this->userRepository->create($data);

                //Send Verification for regular user
                if (!$isAutoCreated) {
                    $user->notify(new UserAccountActivation());
                } //End if
            } else {
                throw new BadRequestHttpException();
            } //End if

            //Assign to the return value
            $objReturnValue = $user;

        } catch(BadRequestHttpException $e) {
            log::error('UserService:register:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserService:register:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends



    public function register(Collection $payload, string $ipAddress=null) {
        $objReturnValue = null;
        $orgId = 0; $userId = 0;

        try {
            //Build user data
            $data = $payload->only([
                'first_name', 'last_name',
                'email', 'phone',
            ])->toArray();

            // Duplicate check
            $isDuplicate=$this->userRepository->exists($data['email'], 'email');
            if (!$isDuplicate) {
                //Create User
                $user = $this->userRepository->create($data);

                //Send Verification for regular user
                if (!$isAutoCreated) {
                    $user->notify(new UserAccountActivation());
                } //End if
            } else {
                throw new BadRequestHttpException();
            } //End if

            //Assign to the return value
            $objReturnValue = $user;

        } catch(BadRequestHttpException $e) {
            log::error('UserService:register:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserService:register:Exception:' . $e->getMessage());
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
    public function getUsersByOrganization(string $orgHash, Collection $payload) {
        $objReturnValue = null;
        $orgId = 0; $userId = 0;

        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');
            if (!empty($user)) {
                if ($user->hasRoles(config('crmomni.settings.default.role.key_super_admin'))) {
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
            log::error('UserService:getUsersByOrganization:NotFoundHttpException:' . $e->getMessage());
            throw new NotFoundHttpException();
        } catch(BadRequestHttpException $e) {
            log::error('UserService:getUsersByOrganization:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserService:getUsersByOrganization:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Fetch Single User Data for an Oraganization OR Current User Profile
     * 
     * @param  \Illuminate\Support\Collection $payload
     * @param  \string $orgHash
     * @param  \string $userHash
     * @param  \boolean $isCurrentUser (default false)
     * 
     * @return mixed
     */
    public function getUserDataByOrganization(Collection $payload, string $orgHash=null, string $userHash=null, bool $isCurrentUser=false) {
        $objReturnValue = null;
        $orgId = 0; $userId = 0;

        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');
            if (!empty($user)) {
                if ($user->hasRoles(config('crmomni.settings.default.role.key_super_admin'))) {
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
            log::error('UserService:getUserDataByOrganization:NotFoundHttpException:' . $e->getMessage());
            throw new NotFoundHttpException();
        } catch(BadRequestHttpException $e) {
            log::error('UserService:getUserDataByOrganization:BadRequestHttpException:' . $e->getMessage());
            throw new BadRequestHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserService:getUserDataByOrganization:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends
