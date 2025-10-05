<?php

namespace Modules\User\Http\Controllers\Backend\Auth;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\User\Http\Requests\Backend\Auth\UserLoginRequest;
use Modules\User\Http\Requests\Backend\Auth\UserLogoutRequest;
use Modules\User\Http\Requests\Backend\Auth\UserForgotRequest;
use Modules\User\Http\Requests\Backend\Auth\UserResetRequest;

use Modules\User\Services\UserAuthService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Controller to Authenticate User
 */
class UserAuthController extends ApiBaseController
{

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * User Login
     *
     * @param \Modules\User\Http\Requests\Backend\Auth\UserLoginRequest $request
     * @param \Modules\User\Services\UserAuthService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/user/login",
     *      tags={"User"},
     *      operationId="api.backend.user.login",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"username", "password"},
     *                  @OA\Property(property="username", description="Enter email address.", type="string"),
     *                  @OA\Property(property="password", description="Enter password.", type="string"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function login(UserLoginRequest $request, UserAuthService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain, true);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create credentials object
            $credentials = collect($request->only('username', 'password'));

            //Authenticate Customer
            $data = $service->authenticate($orgHash, $credentials, $ipAddress);

            //Send response data
            return $this->response->success(compact('data'));
        } catch(Exception $e) {
            throw $e;
        }
    } //Function ends


    /**
     * User Logout or Revoke Token
     *
     * @param \Modules\User\Http\Requests\Backend\Auth\UserLogoutRequest $request
     * @param \Modules\User\Services\UserAuthService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *      path="/user/logout",
     *      tags={"User"},
     *      operationId="api.backend.user.logout",
     *      security={{"omni_token":{}}},
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function logout(UserLogoutRequest $request, UserAuthService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Logout customer
            $data = $service->logout($orgHash, $payload, $ipAddress);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(Exception $e) {
            throw $e;
        }
    } //Function ends


    /**
     * Forgot Password for User
     *
     * @param \Modules\User\Http\Requests\Backend\Auth\UserForgotRequest $request
     * @param \Modules\User\Services\UserAuthService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/user/forgot",
     *      tags={"User"},
     *      operationId="api.backend.user.forgot",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  required={"email"},
     *                  @OA\Property(property="email", description="Enter email address.", type="string")
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function forgot(UserForgotRequest $request, UserAuthService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request->only('email'));

            //Forgot password request
            $data = $service->sendForgotPasswordResetLink($orgHash, $payload, $ipAddress);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(Exception $e) {
            throw $e;
        }
    } //Function ends

    
    /**
     * Reset Password for User
     *
     * @param \Modules\User\Http\Requests\Backend\Auth\UserResetRequest $request
     * @param \Modules\User\Services\UserAuthService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/user/reset",
     *      tags={"User"},
     *      operationId="api.backend.user.reset",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  required={"token", "email", "password", "password_confirmation"},
     *                  @OA\Property(property="token", description="Enter forgot password token sent to the email.", type="string"),
     *                  @OA\Property(property="email", description="Enter email address.", type="string"),
     *                  @OA\Property(property="password", description="Enter new password.", type="string"),
     *                  @OA\Property(property="password_confirmation", description="Re-type new password.", type="string"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function reset(UserResetRequest $request, UserAuthService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Reset Password request
            $data = $service->resetPassword($orgHash, $payload, $ipAddress);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(Exception $e) {
            throw $e;
        }
    } //Function ends

} //Class ends
