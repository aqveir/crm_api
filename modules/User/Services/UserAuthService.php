<?php

namespace Modules\User\Services;

use Config;
use Carbon\Carbon;

use Modules\User\Models\User\User;

use Modules\Core\Repositories\Organization\OrganizationRepository;
use Modules\User\Repositories\User\UserRepository;
use Modules\User\Repositories\User\UserAvailabilityRepository;

use Modules\Core\Services\BaseService;

use Modules\User\Events\UserLoginEvent;
use Modules\User\Events\UserLogoutEvent;

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

use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Support\Facades\Password;

/**
 * Class AuthService
 * @package Modules\Core\Services
 */
class UserAuthService extends BaseService
{
    /**
     * @var Modules\Core\Repositories\Organization\OrganizationRepository
     */
    protected $organizationRepository;

    /**
     * @var Modules\User\Repositories\User\UserRepository
     */
    protected $userRepository;


    /**
     * @var \Illuminate\Auth\AuthManager
     */
    protected $authManager;


    /**
     * @var \Illuminate\Auth\Passwords\PasswordBrokerManager
     */
    protected $passwordBrokerManager;


    /**
     * AuthService constructor.
     * @param \Modules\Core\Repositories\Organization\OrganizationRepository    $organizationRepository
     * @param \Illuminate\Auth\AuthManager                                      $authManager
     * @param \Illuminate\Auth\Passwords\PasswordBrokerManager                  $passwordBrokerManager
     * @param \Modules\User\Repositories\User\UserRepository                    $userRepository
     */
    public function __construct(
        OrganizationRepository              $organizationRepository,
        AuthManager                         $authManager, 
        PasswordBrokerManager               $passwordBrokerManager, 
        UserRepository                      $userRepository
    )
    {
        $this->organizationRepository       = $organizationRepository;
        $this->authManager                  = $authManager;
        $this->passwordBrokerManager        = $passwordBrokerManager;
        $this->userRepository               = $userRepository;
    } //Function ends


    /**
     * Authenticate User
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $credentials
     * @param \string $ipAddress (optional)
     *
     * @return mixed
     */
    public function authenticate(string $orgHash, Collection $credentials, string $ipAddress='0.0.0.0')
    {
        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if (!$organization['is_active']) {
                throw new UnauthorizedHttpException('Organization is inactive.');
            } //End if

            //Create condition for 
            $data = array_merge(
                $credentials->toArray(),
                ['is_active' => 1, 'is_verified' => 1, 'is_remote_access_only' => 0]
            );

            //Authenticate User
            $token = $this->guard()->attempt($data);
            if(!empty($token)) {

                //Check Organization Status
                $user = $this->guard()->user();
                $org = (empty($user))?null:($user->organization);

                if(empty($org))
                {
                    throw new UnauthorizedHttpException('ERROR_USER_AUTH_ORG_ACCESS');
                } //End if
            } else {
                //Check if user exists and updated failed data
                $user = $this->userRepository->getByColumn($credentials['username'], 'username');

                //Update user table
                if($user && $user['is_remote_access_only']==0) {
                    $data = [
                        'failed_attempts' => $user['failed_attempts']+1,
                        'is_active' => ($user['max_failed_attempts']>0)?(($user['max_failed_attempts']>($user['failed_attempts']+1))):$user['is_active']
                    ];
                    $this->userRepository->update($credentials['username'], 'username', $data);
                } //End if 
                
                throw new AccessDeniedHttpException('ERROR_USER_AUTH_CREDENTIALS_PLUS_ACCESS');
            } //End if

            //Update User data after successful authentication
            $data = [
                'last_login_at' => Carbon::now(),
                'failed_attempts' => 0,
            ];
            $this->userRepository->update($credentials['username'], 'username', $data);

            //Attach token and other params
            $user['token_type'] = 'bearer';
            $user['token'] = $token;
            $user['created_on'] = time();
            $user['expires_in'] = $this->guard()->factory()->getTTL() * 60;
            $user['privileges'] = $user->getActivePrivileges($organization['id']);
            $user['reportees'] = [];
            $user['settings'] = $user->hasRoles(['super_admin']);

            //Get Users unread notifications
            $user->unreadNotifications;

            //Raise event: User Login
            event(new UserLoginEvent($organization, $user, $ipAddress));

            return $user;
        } catch(AccessDeniedHttpException $e) {
            log::error('UserAuthService:authenticate:AccessDeniedHttpException:' . $e->getMessage());
            throw $e;
        } catch(UnauthorizedHttpException $e) {
            log::error('UserAuthService:authenticate:UnauthorizedHttpException:' . $e->getMessage());
            throw $e;
        } catch(Exception $e) {
            log::error('UserAuthService:authenticate:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try Catch ends
    } //Function ends


    /**
     * Sedn the forgot password email to user
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $request
     * @param \string $ipAddress (optional)
     *
     * @return bool
     */
    public function sendForgotPasswordResetLink(string $orgHash, Collection $request, string $ipAddress='0.0.0.0')
    {
        $objReturnValue = false;

        try {
            $response = $this->passwordBrokerManager->sendResetLink(
                $request->only('email')->toArray()
            );

            return $response === Password::RESET_LINK_SENT;
        } catch(AccessDeniedHttpException $e) {
            log::error('UserAuthService:sendForgotPasswordResetLink:AccessDeniedHttpException:' . $e->getMessage());
            throw $e;
        } catch(UnauthorizedHttpException $e) {
            log::error('UserAuthService:sendForgotPasswordResetLink:UnauthorizedHttpException:' . $e->getMessage());
            throw $e;
        } catch(Exception $e) {
            log::error('UserAuthService:sendForgotPasswordResetLink:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try Catch ends
    } //Function ends


    /**
     * Reset the password for the user
     * 
     * @param \string $orgHash
     * @param \Illuminate\Support\Collection $credentials
     * @param \string $ipAddress (optional)
     *
     * @return bool
     */
    public function resetPassword(string $orgHash, Collection $request, string $ipAddress='0.0.0.0')
    {
        try {
            $response = $this->passwordBrokerManager->reset(
                $request->only('email', 'password', 'password_confirmation', 'token')->toArray(),
                function (User $user, string $password) {
                    $user->password = $password;
                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            if($response !== Password::PASSWORD_RESET) {
                if($response === Password::INVALID_TOKEN) { throw new AccessDeniedHttpException('NOTIFICATION.USER_AUTH.RESET_PASSWORD.ERROR_TOKEN_EXPIRED'); }
                elseif($response === Password::INVALID_USER) { throw new BadRequestHttpException('NOTIFICATION.USER_AUTH.RESET_PASSWORD.ERROR_USER_INVALID'); }
                else { throw new HttpException(500, 'NOTIFICATION.USER_AUTH.RESET_PASSWORD.ERROR_MESSAGE'); }
            } //End if

            return $response === Password::PASSWORD_RESET;
        } catch(Exception $e) {
            throw $e;
        } //Try-catch ends
    } //Function ends


    /**
     * Logout the user
     * 
     * @param \string $orgHash 
     * @param \Illuminate\Support\Collection $credentials
     * @param \string $ipAddress (optional)
     *
     * @return mixed
     */
    public function logout(string $orgHash, Collection $credentials, string $ipAddress='0.0.0.0')
    {
        $objReturnValue = false;

        try {
            //Get organization data
            $organization = $this->getOrganizationByHash($orgHash);
            if (!$organization['is_active']) {
                throw new UnauthorizedHttpException('Organization is inactive.');
            } //End if
            
            //Get current user
            $user = $this->getCurrentUser();
            if ($user)
            {
                //Initialize params
                $objReturnValue = $user;

                //Pass true to force the token to be blacklisted "forever"
                $objReturnValue = $this->guard('backend')->logout(true);

                //Raise event: User Logout
                event(new UserLogoutEvent($organization, $user, $ipAddress));
            } else {
                throw new UnauthorizedHttpException('ERROR_USER_AUTH_ACCESS');
            } //End if
        } catch(AccessDeniedHttpException $e) {
            log::error('UserAuthService:logout:AccessDeniedHttpException:' . $e->getMessage());
            throw $e;
        } catch(UnauthorizedHttpException $e) {
            log::error('UserAuthService:logout:UnauthorizedHttpException:' . $e->getMessage());
            throw $e;
        } catch(Exception $e) {
            log::error('UserAuthService:logout:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try Catch ends

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
            throw $e;
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
            throw $e;
        } catch(Exception $e) {
            log::error('UserService:register:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends
