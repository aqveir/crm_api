<?php

namespace Modules\User\Services\User;

use Config;
use Carbon\Carbon;

use Modules\User\Models\User\User;

use Modules\User\Repositories\User\UserRepository;

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
     *
     * @param \Illuminate\Auth\AuthManager                              $authManager
     * @param \Illuminate\Auth\Passwords\PasswordBrokerManager          $passwordBrokerManager
     * @param \Modules\User\Repositories\User\UserRepository            $userRepository
     */
    public function __construct(
        AuthManager                         $authManager, 
        PasswordBrokerManager               $passwordBrokerManager, 
        UserRepository                      $userRepository
    )
    {
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
                if($user) {
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
            $user['privileges'] = $user->getActivePrivileges();
            $user['reportees'] = [];
            $user['settings'] = $user->hasRoles(['super_admin']);

            //Raise event: User Login
            event(new UserLoginEvent($user));

            return $user;
        } catch(AccessDeniedHttpException $e) {
            log::error('UserAuthService:authenticate:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(UnauthorizedHttpException $e) {
            log::error('UserAuthService:authenticate:UnauthorizedHttpException:' . $e->getMessage());
            throw new UnauthorizedHttpException($e->getMessage());
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
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(UnauthorizedHttpException $e) {
            log::error('UserAuthService:sendForgotPasswordResetLink:UnauthorizedHttpException:' . $e->getMessage());
            throw new UnauthorizedHttpException($e->getMessage());
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
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, $password) {
                    $user->password = $password;
                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            if($response !== Password::PASSWORD_RESET) {
                if($response === Password::INVALID_TOKEN) { throw new AccessDeniedHttpException('Token Expired'); }
                elseif($response === Password::INVALID_USER) { throw new BadRequestHttpException(); }
                else {throw new HttpException(500);}
            } //End if

            return $response === Password::PASSWORD_RESET;
        } catch(Exception $e) {
            return false;
        } //Try-catch ends
    } //Function ends


    /**
     * Logout the user
     * 
     * @param \Illuminate\Support\Collection $credentials
     *
     * @return mixed
     */
    public function logout(Collection $credentials)
    {
        $objReturnValue = false;

        try {
            //Get current user
            $user = $this->getCurrentUser();
            if ($user)
            {
                //Initialize params
                $objReturnValue = $user;

                //Pass true to force the token to be blacklisted "forever"
                $objReturnValue = $this->guard('backend')->logout(true);

                //Raise event: User Logout
                event(new UserLogoutEvent($user));
            } else {
                throw new UnauthorizedHttpException('ERROR_USER_AUTH_ACCESS');
            } //End if
        } catch(AccessDeniedHttpException $e) {
            log::error('UserAuthService:logout:AccessDeniedHttpException:' . $e->getMessage());
            throw new AccessDeniedHttpException($e->getMessage());
        } catch(UnauthorizedHttpException $e) {
            log::error('UserAuthService:logout:UnauthorizedHttpException:' . $e->getMessage());
            throw new UnauthorizedHttpException($e->getMessage());
        } catch(Exception $e) {
            log::error('UserAuthService:logout:Exception:' . $e->getMessage());
            throw new HttpException(500);
        } //Try Catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends
