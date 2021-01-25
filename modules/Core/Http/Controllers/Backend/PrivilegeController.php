<?php

namespace Modules\Core\Http\Controllers\Backend;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;

use Modules\Core\Http\Requests\Backend\Privilege\FetchPrivilegeRequest;
use Modules\Core\Http\Requests\Backend\Privilege\CreatePrivilegeRequest;
use Modules\Core\Http\Requests\Backend\Privilege\UpdatePrivilegeRequest;

use Modules\Core\Services\Privilege\PrivilegeService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Controller for Privilege Data
 */
class PrivilegeController extends ApiBaseController
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
     * Get All Privileges
     * 
     * @param \Modules\Core\Http\Requests\Backend\Privilege\FetchPrivilegeRequest $request
     * @param \Modules\Core\Services\Privilege\PrivilegeService $service
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/privilege",
     *     tags={"Privilege"},
     *     operationId="api.privilege.index",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=401, description="Authorization Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(FetchPrivilegeRequest $request, PrivilegeService $service)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Create payload
            $payload = collect($request);

            //Fetch all privilege data
            $data = $service->index($orgHash, $payload);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Get Privilege Data by Key
     * 
     * @param \Modules\Core\Http\Requests\Backend\Privilege\FetchPrivilegeRequest $request
     * @param \Modules\Core\Services\Privilege\PrivilegeService $service
     * @param \string $key
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/privilege/{key}",
     *     tags={"Privilege"},
     *     operationId="api.privilege.show",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Parameter(name="key", in="path", description="Key", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show(FetchPrivilegeRequest $request, PrivilegeService $service, string $key)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Create payload
            $payload = collect($request);

            //Fetch all privilege data
            $data = $service->show($orgHash, $payload, $key);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Create Privilege Data
     * 
     * @param \Modules\Core\Http\Requests\Backend\Privilege\CreatePrivilegeRequest $request
     * @param \Modules\Core\Services\Privilege\PrivilegeService $service
     * @param \string $key
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/privilege",
     *     tags={"Privilege"},
     *     operationId="api.privilege.create",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(CreatePrivilegeRequest $request, PrivilegeService $service)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Create payload
            $payload = collect($request);

            //Fetch all privilege data
            $data = $service->show($orgHash, $payload, $key);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Update Privilege Data by Key
     * 
     * @param \Modules\Core\Http\Requests\Backend\Privilege\UpdatePrivilegeRequest $request
     * @param \Modules\Core\Services\Privilege\PrivilegeService $service
     * @param \string $key
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/privilege/{key}",
     *     tags={"Privilege"},
     *     operationId="api.privilege.update",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Parameter(name="key", in="path", description="Key", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(UpdatePrivilegeRequest $request, PrivilegeService $service, string $key)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Create payload
            $payload = collect($request);

            //Fetch all privilege data
            $data = $service->show($orgHash, $payload, $key);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

    
    /**
     * Delete Privilege Data by Key
     * 
     * @param \Modules\Core\Http\Requests\Backend\Privilege\FetchPrivilegeRequest $request
     * @param \Modules\Core\Services\Privilege\PrivilegeService $service
     * @param \string $key
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/privilege/{key}",
     *     tags={"Privilege"},
     *     operationId="api.privilege.delete",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Parameter(name="key", in="path", description="Key", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function delete(FetchPrivilegeRequest $request, PrivilegeService $service, string $key)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Create payload
            $payload = collect($request);

            //Fetch all privilege data
            $data = $service->show($orgHash, $payload, $key);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends