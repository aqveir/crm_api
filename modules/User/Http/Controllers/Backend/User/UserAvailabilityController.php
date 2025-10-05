<?php

namespace Modules\User\Http\Controllers\Backend\User;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;

use Modules\User\Services\UserService;
use Modules\User\Services\UserAvailabilityService;

use Modules\User\Http\Requests\Backend\User\UserStatusRequest;
use Modules\User\Http\Requests\Backend\User\SaveUserAvailabilityRequest;

use Modules\User\Transformers\Responses\UserStatusJsonResponseResource;
use Modules\User\Transformers\Responses\UserStatusTextResponseResource;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Modules\Core\Exceptions\ExistingDataException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserAvailabilityController extends ApiBaseController
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
     * Get Current User Availability Status
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\User\Services\UserAvailabilityService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/user/status",
     *     tags={"User"},
     *     operationId="api.backend.user.availability.status.view",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function view(Request $request, UserAvailabilityService $service, string $subdomain)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Set Status
            $data = $service->fetch($orgHash, $payload);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(NotFoundHttpException $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Get User Availability Status
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\User\Services\UserAvailabilityService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/user/{hash}/status",
     *     tags={"User"},
     *     operationId="api.backend.user.availability.status.show",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show(Request $request, UserAvailabilityService $service, string $subdomain, string $hash)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Set Status
            $data = $service->fetch($orgHash, $payload, $hash);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(NotFoundHttpException $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Set User Availability Status
     *
     * @param \Modules\User\Http\Requests\Backend\User\SaveUserAvailabilityRequest $request
     * @param \Modules\User\Services\UserAvailabilityService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/user/status/{key}",
     *     tags={"User"},
     *     operationId="api.backend.user.availability.status",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(
     *          in="path", name="key", description="Enter user availability status (i.e. online, away)", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(SaveUserAvailabilityRequest $request, UserAvailabilityService $service, string $subdomain, string $key)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Set Status
            $data = $service->update($payload, $key, $ipAddress);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(ExistingDataException $e) {
            return $this->response->fail(['EXCEPTION_EXISTING_DATA'], Response::HTTP_BAD_REQUEST);
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
     * @param \Modules\User\Services\UserService $userService
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
            $phoneFormat = ($request->has('phoneformat') && ($request['phoneformat']))?$request['phoneformat']:'E164';

            //Send response data
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
