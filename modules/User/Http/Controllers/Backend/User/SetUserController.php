<?php

namespace Modules\User\Http\Controllers\Backend\User;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\User\Http\Requests\Backend\User\RegisterUserRequest;
use Modules\User\Http\Requests\Backend\User\CreateUserRequest;
use Modules\User\Http\Requests\Backend\User\UpdateUserRequest;
use Modules\User\Http\Requests\Backend\User\UserVerifyRequest;
use Modules\User\Http\Requests\Backend\User\UserActivateRequest;

use Modules\User\Services\User\UserService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SetUserController extends ApiBaseController
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
     * Register User
     *
     * @param \Modules\User\Http\Requests\Backend\User\RegisterUserRequest $request
     * @param \Modules\User\Services\User\UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/user/register",
     *     tags={"User"},
     *     operationId="api.backend.user.register",
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function register(RegisterUserRequest $request, UserService $userService)
    {   
        try {
            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Create customer
            $data = $userService->register($payload, $ipAddress);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Create User
     *
     * @param \Modules\User\Http\Requests\Backend\User\CreateUserRequest $request
     * @param \Modules\User\Services\User\UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/user",
     *     tags={"User"},
     *     operationId="api.backend.user.create",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(CreateUserRequest $request, UserService $userService)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Create customer
            $data = $userService->create($orgHash, $payload, $ipAddress);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Update User
     *
     * @param \Modules\User\Http\Requests\Backend\User\UpdateUserRequest $request
     * @param \Modules\User\Services\User\UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/user/{hash}",
     *     tags={"User"},
     *     operationId="api.backend.user.update",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(UpdateUserRequest $request, UserService $userService, string $hash)
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


    /**
     * Verify User Account & Email
     *
     * @param \Modules\User\Http\Requests\Backend\User\UserVerifyRequest $request
     * @param \Modules\User\Services\User\UserService $userService
     * @param \string $token
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/user/verify/{token}",
     *     tags={"User"},
     *     operationId="api.backend.user.verify",
     *     @OA\Parameter(
     *          in="path", name="token", description="Email verification token", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Parameter(
     *          in="query", name="email", description="Enter valid email address.", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function verify(UserVerifyRequest $request, UserService $userService, string $token)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Create payload
            $payload = collect($request);

            //Verify User Email
            $data = $userService->verify($orgHash, $payload, $token);

            //Send http status out
            return $this->response->success(compact('data'));
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Activate User Account & Email
     *
     * @param \Modules\User\Http\Requests\Backend\User\UserActivateRequest $request
     * @param \Modules\User\Services\User\UserService $userService
     * @param \string $token
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/user/activate/{token}",
     *     tags={"User"},
     *     operationId="api.backend.user.activate",
     *     @OA\Parameter(
     *          in="path", name="token", description="Email activation token", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          in="query", name="email", description="Enter valid email address.", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function activate(UserActivateRequest $request, UserService $userService, string $token)
    {
        try {
            //Create payload
            $payload = collect($request);

            $data = $userService->activate($payload, $token);

            //Send http status out
            return $this->response->success(compact('data'));
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends
