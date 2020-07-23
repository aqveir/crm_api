<?php

namespace Modules\User\Http\Controllers\Backend\Auth;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\User\Http\Requests\Backend\Auth\UserLoginRequest;
use Modules\User\Http\Requests\Backend\Auth\UserLogoutRequest;
use Modules\User\Http\Requests\Backend\Auth\UserForgotRequest;
use Modules\User\Http\Requests\Backend\Auth\UserResetRequest;

use Modules\User\Services\User\UserAuthService;

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
     * @param \Modules\User\Services\User\UserAuthService $userAuthService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/user/login",
     *      tags={"User"},
     *      operationId="api.backend.user.login",
     *      @OA\Parameter(
     *          ref="#/components/parameters/organization_key",
     *      ),
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
    public function login(UserLoginRequest $request, UserAuthService $userAuthService)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create credentials object
            $credentials = collect($request->only('username', 'password'));

            //Authenticate Customer
            $data = $userAuthService->authenticate($orgHash, $credentials, $ipAddress);

            //Send http status out
            return $this->response->success(compact('data'));
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Customer Logout or Revoke Token
     *
     * @param \Modules\User\Http\Requests\Backend\Auth\UserLogoutRequest $request
     * @param \Modules\User\Services\User\UserAuthService $userAuthService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *      path="/user/logout",
     *      tags={"User"},
     *      operationId="api.backend.user.logout",
     *      security={{"JWT_Bearer_Auth":{}}},
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function logout(UserLogoutRequest $request, UserAuthService $userAuthService)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Logout customer
            $data = $userAuthService->logout($orgHash, $payload, $ipAddress);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    public function forgot(UserForgotRequest $request, UserAuthService $userAuthService)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request->only('email'));

            //Forgot password request
            $data = $userAuthService->sendForgotPasswordResetLink($orgHash, $payload, $ipAddress);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

    
    public function reset(UserResetRequest $request, UserAuthService $userAuthService)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request->only('password', 'new_password'));

            //Reset Password request
            $data = $userAuthService->resetPassword($orgHash, $payload, $ipAddress);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends
