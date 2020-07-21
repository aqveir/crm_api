<?php

namespace Modules\User\Services\User;

use Config;
use Carbon\Carbon;

use Modules\User\Models\User\User;
use Modules\User\Models\User\Traits\Action\UserAction;
use Modules\Core\Models\Lookup\Traits\Action\LookupValueAction;
use Modules\User\Models\User\Traits\Action\UserAvailabilityAction;

use Modules\User\Repositories\User\UserRepository;

use Modules\Core\Services\BaseService;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class AuthService
 * @package Modules\Core\Services
 */
class UserAuthService extends BaseService
{
    /**
     * @var \Illuminate\Auth\Passwords\PasswordBrokerManager
     */
    protected $passwordBrokerManager;


    /**
     * @var Modules\Core\Repositories\User\UserRepository
     */
    protected $userrepository;


    /**
     * @var \Modules\Core\Models\User\User
     */
    protected $user;


    /**
     * AuthService constructor.
     *
     * @param \Illuminate\Auth\Passwords\PasswordBrokerManager          $passwordBrokerManager
     * @param \Modules\Core\Models\User\User                            $user
     */
    public function __construct(
        PasswordBrokerManager $passwordBrokerManager,
        UserRepository $userrepository,
        User $user)
    {
        $this->passwordBrokerManager = $passwordBrokerManager;
        $this->userrepository        = $userrepository;
        $this->user                  = $user;
    } //Function ends


    /**
     * @param $credentials
     *
     * @return bool
     */
    public function authenticate(string $orgHash, $credentials)
    {
        try {
            //Create condition for 
            $credentials = array_merge(
                $credentials,
                ['is_active' => 1, 'is_verified' => 1, 'is_remote_access_only' => 0]
            );

            //Authenticate User
            $token = $this->guard()->attempt($credentials);
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
                $user = $this->userrepository->getByColumn($credentials['username'], 'username');

                //Update user table
                if($user) {
                    $data = [
                        'failed_attempts' => $user['failed_attempts']+1,
                        'is_active' => ($user['max_failed_attempts']>0)?(($user['max_failed_attempts']>($user['failed_attempts']+1))):$user['is_active']
                    ];
                    $this->userrepository->update($credentials['username'], 'username', $data);
                } //End if 
                
                throw new AccessDeniedHttpException('ERROR_USER_AUTH_CREDENTIALS_PLUS_ACCESS');
            } //End if

            //Update User data after successful authentication
            $data = [
                'last_login_at' => Carbon::now(),
                'failed_attempts' => 0,
            ];
            $this->userrepository->update($credentials['username'], 'username', $data);

            return $token;
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
     * Create a Backend User
     * 
     * @param \Illuminate\Http\Request  $request
     * @param bool                      $isRemoteAccessOnly
     *
     * @return mixed
     */
    public function createUser(Request $request, bool $isRemoteAccessOnly=false)
    {
        $objReturnValue=null;
        try {
            //Authenticated User
            $user = $this->getCurrentUser('backend');

            //Payload for user creation
            $payload = $request->only('username', 'password', 'email', 'first_name', 'last_name');

            // Duplicate check
            $isDuplicate=$this->userrepository->exists($payload['username'], 'username');
            if(!$isDuplicate) {

                //Generate the data payload to create user
                $payload = array_merge(
                    $payload,
                    [
                        'org_id' => $user['org_id'],
                        'is_active' => 1, 
                        'is_verified' => 0, 
                        'is_remote_access_only' => $isRemoteAccessOnly,
                        'created_by' => $user['id']
                    ]
                );

                //Create User
                $objReturnValue = $this->userrepository->create($payload);
            } else {
                throw new BadRequestHttpException();
            } //End if
        } catch(Exception $e) {
            throw new BadRequestHttpException();
        }
        return $objReturnValue;
    } //Function ends


    /**
     * Get user details
     *
     * @return object (User)
     */
    public function getUserData(string $token)
    {
        $objReturnValue = null;

        try {
            $user = $this->getCurrentUser();
            $user = $user->load('roles', 'grant_privileges');

            if($user)
            {
                //Initialize params
                $objReturnValue = $user;
                $userPrivileges=[];
          
                //Check Roles with active privileges
                $userRoles = $user->roles; //$this->getUserRole($query['org_id'], $query['id']);
                if($user->roles) {
                    //Iterate the roles for the user
                    foreach($user->roles as $role) {
                        if(!empty($role)) {
                            //Iterate the privileges in each role
                            foreach($role->active_privileges as $privilege) {
                                //Duplicate check to add the privileges
                                if(!in_array($privilege, $userPrivileges, TRUE)) {
                                    array_push($userPrivileges, $privilege);
                                } //End if
                            } //Loop ends (privileges)
                        } //End if
                    } //Loop ends (roles)
                } //End if
                
                //Check for extra granted privileges
                foreach($user->grant_privileges as $privilege) {
                    //Duplicate check to add the privileges
                    if(!in_array($privilege, $userPrivileges, TRUE)) {
                        array_push($userPrivileges, $privilege);
                    } //End if
                } //Loop ends (privileges)

                $objReturnValue['privileges']=$userPrivileges;
                $objReturnValue->makeHidden('privileges');
            } //End if
        } catch(Exception $e) {
            log::error($e);
            throw new HttpException(500);
        }

        return $objReturnValue;
    } //Function ends


    /**
     * Sedn the forgot password email to user
     * 
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public function sendForgotPasswordResetLink(Request $request)
    {
        $response = $this->passwordBrokerManager->sendResetLink(
            $request->only('email')
        );

        return $response === Password::RESET_LINK_SENT;
    } //Function ends


    /**
     * Reset the password for the user
     * 
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public function resetPassword(Request $request)
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
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function logout(Request $request)
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

                //Set User Status as offline
                $response = $userService->setUserOffline($user, $user['id']);

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
