<?php

namespace Modules\User\Http\Controllers\Backend\User;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\User\Models\User\User;

use Modules\Core\Http\Controllers\ApiBaseController;

use Modules\User\Http\Requests\Backend\User\GetUserRequest;
use Modules\User\Http\Requests\Backend\User\CreateUserRequest;
use Modules\User\Http\Requests\Backend\User\UpdateUserRequest;
use Modules\User\Http\Requests\Backend\User\DeleteUserRequest;
use Modules\User\Http\Requests\Backend\User\UserExistsRequest;

use Modules\User\Transformers\Responses\UserResource;
use Modules\User\Transformers\Responses\UserMinifiedResource;

use Modules\User\Services\UserService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserController extends ApiBaseController
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
     * Get All Users for an Organization
     *
     * @param \Modules\User\Http\Requests\Backend\User\GetUserRequest $request
     * @param \Modules\User\Services\UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/user",
     *     tags={"User"},
     *     operationId="api.backend.user.index",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(GetUserRequest $request, UserService $userService, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch Users data for Organization
            $response = $userService->getAll($orgHash, $payload);

            //Transform data
            $data = new UserMinifiedResource(collect($response));

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Show User By Identifier
     *
     * @param \Modules\User\Http\Requests\Backend\User\GetUserRequest $request
     * @param \Modules\User\Services\UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/user/{hash}",
     *     tags={"User"},
     *     operationId="api.backend.user.show",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show(GetUserRequest $request, UserService $service, string $subdomain, User $user)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch User record
            $result = $service->show($orgHash, $payload, $user['hash']);

            //Transform data
            $data = new UserResource($result);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Show User Profile Data By User Token
     *
     * @param \Modules\User\Http\Requests\Backend\User\GetUserRequest $request
     * @param \Modules\User\Services\UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/user/profile",
     *     tags={"User"},
     *     operationId="api.backend.user.profile",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function profile(GetUserRequest $request, UserService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch User record
            $result = $service->show($orgHash, $payload, null, true);

            //Transform data
            $data = new UserResource($result);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Check if the user exists
     *
     * @param \Modules\User\Http\Requests\Backend\User\UserExistsRequest $request
     * @param \Modules\User\Services\UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/user/exists",
     *      tags={"User"},
     *      operationId="api.backend.user.exists",
     *      @OA\Parameter(
     *          ref="#/components/parameters/organization_key",
     *      ),
     *      @OA\Parameter(
     *          in="query", name="phone", description="Enter phone number w/o country code.", required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          in="query", name="email", description="Enter email address.", required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function exists(UserExistsRequest $request, UserService $userService, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            $data = $userService->exists($orgHash, $payload);

            //Send response data
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
     * @param \Modules\User\Services\UserService $userService
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
    public function create(CreateUserRequest $request, UserService $userService, string $subdomain)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Create customer
            $data = $userService->create($orgHash, $payload, $ipAddress);

            //Send response data
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
     * @param \Modules\User\Services\UserService $userService
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
    public function update(UpdateUserRequest $request, UserService $service, string $subdomain, User $user)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Logout customer
            $data = $service->update($orgHash, $payload, $user['hash'], $ipAddress);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Delete User
     *
     * @param \Modules\User\Http\Requests\Backend\User\DeleteUserRequest $request
     * @param \Modules\User\Services\UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/user/{hash}",
     *     tags={"User"},
     *     operationId="api.backend.user.delete",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy(DeleteUserRequest $request, UserService $service, string $subdomain, User $user)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Logout customer
            $data = $service->delete($orgHash, $payload, $user['hash'], $ipAddress);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends
