<?php

namespace Modules\Core\Http\Controllers\Backend;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Modules\Core\Models\Privilege\Privilege;

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
        $this->authorizeResource(Privilege::class, 'privilege');
    }


    /**
     * Get All Privileges
     * 
     * @param \Modules\Core\Http\Requests\Backend\Privilege\FetchPrivilegeRequest $request
     * @param \Modules\Core\Services\Privilege\PrivilegeService $service
     * @param \string $subdomain
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
    public function index(FetchPrivilegeRequest $request, PrivilegeService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch all privilege data
            $data = $service->index($orgHash, $payload);

            //Send response data
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
     * @param \string $subdomain
     * @param \Modules\Core\Models\Privilege\Privilege $privilege
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
    public function show(FetchPrivilegeRequest $request, PrivilegeService $service, string $subdomain, Privilege $privilege)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch all privilege data
            $data = $service->show($orgHash, $payload, $privilege['key']);

            //Send response data
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
     * @param \string $subdomain
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
    public function create(CreatePrivilegeRequest $request, PrivilegeService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Create privilege data
            $data = $service->create($orgHash, $payload);

            //Send response data
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
     * @param \string $subdomain
     * @param \Modules\Core\Models\Privilege\Privilege $privilege
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
    public function update(UpdatePrivilegeRequest $request, PrivilegeService $service, string $subdomain, Privilege $privilege)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Update privilege data
            $data = $service->update($orgHash, $payload, $privilege['key']);

            //Send response data
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
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Core\Services\Privilege\PrivilegeService $service
     * @param \string $subdomain
     * @param \Modules\Core\Models\Privilege\Privilege $privilege
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/privilege/{key}",
     *     tags={"Privilege"},
     *     operationId="api.privilege.destroy",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Parameter(name="key", in="path", description="Key", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy(Request $request, PrivilegeService $service, string $subdomain, Privilege $privilege)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Delete privilege data by key
            $data = $service->delete($orgHash, $payload, $privilege['key']);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends