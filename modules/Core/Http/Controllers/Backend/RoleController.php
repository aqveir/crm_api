<?php

namespace Modules\Core\Http\Controllers\Backend;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Modules\Core\Models\Role\Role;

use Modules\Core\Http\Controllers\ApiBaseController;

use Modules\Core\Http\Requests\Backend\Role\FetchRoleRequest;
use Modules\Core\Http\Requests\Backend\Role\CreateRoleRequest;
use Modules\Core\Http\Requests\Backend\Role\UpdateRoleRequest;

use Modules\Core\Services\Role\RoleService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Controller for Role Data
 */
class RoleController extends ApiBaseController
{

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(Role::class, 'role');
    }


    /**
     * Get All Roles
     * 
     * @param \Modules\Core\Http\Requests\Backend\Role\FetchRoleRequest $request
     * @param \Modules\Core\Services\Role\RoleService $service
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/role",
     *     tags={"Role"},
     *     operationId="api.role.index",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=401, description="Authorization Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(FetchRoleRequest $request, RoleService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch all role data
            $data = $service->index($orgHash, $payload);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(NotFoundHttpException $e) {
            return $this->response->fail([], Response::HTTP_NOT_FOUND);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Get Role Data by Key
     * 
     * @param \Modules\Core\Http\Requests\Backend\Role\FetchRoleRequest $request
     * @param \Modules\Core\Services\Role\RoleService $service
     * @param \string $key
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/role/{key}",
     *     tags={"Role"},
     *     operationId="api.role.show",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Parameter(name="key", in="path", description="Key", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show(FetchRoleRequest $request, RoleService $service, string $subdomain, Role $role)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch all role data
            $data = $service->show($orgHash, $payload, $role['key']);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Create Role Data
     * 
     * @param \Modules\Core\Http\Requests\Backend\Role\CreateRoleRequest $request
     * @param \Modules\Core\Services\Role\RoleService $service
     * @param \string $key
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/role",
     *     tags={"Role"},
     *     operationId="api.role.create",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(CreateRoleRequest $request, RoleService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch all role data
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
     * Update Role Data by Key
     * 
     * @param \Modules\Core\Http\Requests\Backend\Role\UpdateRoleRequest $request
     * @param \Modules\Core\Services\Role\RoleService $service
     * @param \string $key
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/role/{key}",
     *     tags={"Role"},
     *     operationId="api.role.update",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Parameter(name="key", in="path", description="Key", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(UpdateRoleRequest $request, RoleService $service, string $subdomain, Role $role)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Create payload
            $payload = collect($request);

            //Fetch all role data
            $data = $service->update($orgHash, $payload, $role['key']);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

    
    /**
     * Delete Role Data by Key
     * 
     * @param \Modules\Core\Http\Requests\Backend\Role\FetchRoleRequest $request
     * @param \Modules\Core\Services\Role\RoleService $service
     * @param \string $key
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/role/{key}",
     *     tags={"Role"},
     *     operationId="api.role.destroy",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Parameter(name="key", in="path", description="Key", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy(FetchRoleRequest $request, RoleService $service, string $subdomain, Role $role)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch all role data
            $data = $service->delete($orgHash, $payload, $role['key']);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends