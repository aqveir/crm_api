<?php

namespace Modules\User\Http\Controllers\Backend\User;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\User\Http\Requests\Backend\User\GetUserRequest;
use Modules\User\Http\Requests\Backend\User\CreateUserRequest;
use Modules\User\Http\Requests\Backend\User\UpdateUserRequest;
use Modules\User\Http\Requests\Backend\User\UserStatusRequest;

use Modules\User\Transformers\Responses\UserResource;
use Modules\User\Transformers\Responses\UserMinifiedResource;
use Modules\User\Transformers\Responses\UserStatusJsonResponseResource;
use Modules\User\Transformers\Responses\UserStatusTextResponseResource;

use Modules\User\Services\User\UserService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GetUserController extends ApiBaseController
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
     * @param \Modules\User\Services\User\UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/organization/{ohash}/user",
     *     tags={"User"},
     *     operationId="api.backend.user.index",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(
     *          in="path", name="ohash", description="Enter roganization code or key", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(GetUserRequest $request, UserService $userService, string $subdomain, string $ohash)
    {
        try {
            //Get Org Hash 
            $orgHash = $ohash;

            //Create payload
            $payload = collect($request);

            //Fetch Users data for Organization
            $response = $userService->getUsersByOrganization($orgHash, $payload);

            //Transform data
            $data = new UserMinifiedResource(collect($response));

            //Send http status out
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
     * @param \Modules\User\Services\User\UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/organization/{ohash}/user/{hash}",
     *     tags={"User"},
     *     operationId="api.backend.user.show",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(
     *          in="path", name="ohash", description="Enter roganization code or key", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show(GetUserRequest $request, UserService $userService, string $subdomain, string $ohash, string $hash)
    {   
        try {
            //Get Org Hash 
            $orgHash = $ohash;

            //Create payload
            $payload = collect($request);

            //Fetch User record
            $result = $userService->getUserDataByOrganization($payload, $orgHash, $hash);

            //Transform data
            $data = new UserResource($result);

            //Send http status out
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
     * @param \Modules\User\Services\User\UserService $userService
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
    public function profile(GetUserRequest $request, UserService $userService, string $subdomain)
    {
        try {
            //Create payload
            $payload = collect($request);

            //Fetch User record
            $data = $userService->getUserDataByOrganization($payload, null, null, true);

            //Send http status out
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
     * @param \Modules\User\Services\User\UserService $userService
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

            $data = $userService->validateUserExists($orgHash, $payload);

            //Send http status out
            return $this->response->success(compact('data'));
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Get Users By Availability Status
     *
     * @param \Modules\User\Http\Requests\Backend\User\UserStatusRequest $request
     * @param \Modules\User\Services\User\UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/user/status/{status}",
     *     tags={"User"},
     *     operationId="api.backend.user.availability.status.detail",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(
     *          in="path", name="status", description="Enter user availability status (i.e. online, away)", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Parameter(
     *          in="query", name="role_key", description="Enter role key", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function detail(UserStatusRequest $request, UserService $service, string $subdomain, string $status)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Set Status
            $response = $service->getUserByStatus($orgHash, $payload, $status);

            //Output formats
            $outputFormat = ($request->has('output'))?$request['output']:'hash,first_name,full_name,phone';
            dd($outputFormat);
            $phoneFormat = ($request->has('phoneformat'))?$request['phoneformat']:'0[number]';

            //Send http status out
            switch ($request->headers->get('CONTENT-TYPE')) {
                case 'application/json':
                    $data = new UserStatusJsonResponseResource(collect($response), $outputFormat, $phoneFormat);
                    return $this->response->success(compact('data'));
                    break;

                case 'application/xml':
                    $data = $response;
                    return $data;
                    break;

                default:
                    $data = new UserStatusTextResponseResource(collect($response), $outputFormat, $phoneFormat);
                    $data = (!empty($data))?implode(",", json_decode(json_encode($data))):null;
                    return $data;
                    break;
            } //Switch ends
            
        } catch(NotFoundHttpException $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends
