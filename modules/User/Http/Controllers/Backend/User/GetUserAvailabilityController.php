<?php

namespace Modules\User\Http\Controllers\Backend\User;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\User\Services\User\UserAvailabilityService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Modules\Core\Exceptions\ExistingDataException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GetUserAvailabilityController extends ApiBaseController
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
     * @param \Modules\User\Services\User\UserAvailabilityService $service
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
    public function view(Request $request, UserAvailabilityService $service)
    {   
        try {
            //Create payload
            $payload = collect($request);

            //Set Status
            $data = $service->fetch($payload);

            //Send http status out
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
     * @param \Modules\User\Services\User\UserAvailabilityService $service
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
    public function show(Request $request, UserAvailabilityService $service, string $hash)
    {   
        try {
            //Create payload
            $payload = collect($request);

            //Set Status
            $data = $service->fetch($payload, $hash);

            //Send http status out
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
     * Get Users By Availability Status
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\User\Services\User\UserAvailabilityService $service
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
    public function detail(Request $request, UserAvailabilityService $service, string $status)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            $contentType = 'application/json';

            //Create payload
            $payload = collect($request);

            //Set Status
            $data = $service->detail($orgHash, $payload, $status);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(NotFoundHttpException $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends
