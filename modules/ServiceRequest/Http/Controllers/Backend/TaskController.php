<?php

namespace Modules\ServiceRequest\Http\Controllers\Backend;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\ServiceRequest\Models\Task;

use Modules\Core\Http\Controllers\ApiBaseController;

use Modules\ServiceRequest\Http\Requests\Backend\Task\CreateTaskRequest;
use Modules\ServiceRequest\Http\Requests\Backend\Task\UpdateTaskRequest;
use Modules\ServiceRequest\Http\Requests\Backend\Task\DeleteTaskRequest;

use Modules\ServiceRequest\Transformers\Responses\TaskResource;
use Modules\ServiceRequest\Transformers\Responses\TaskMinifiedResource;

use Modules\ServiceRequest\Services\TaskService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class TaskController extends ApiBaseController
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
     * Get All Tasks for an Organization
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\ServiceRequest\GetServiceRequestRequest $request
     * @param \Modules\ServiceRequest\Services\TaskService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/task",
     *     tags={"Task"},
     *     operationId="api.backend.servicerequest.task.index",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(GetServiceRequestRequest $request, TaskService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch Users data for Organization
            $response = $service->getAll($orgHash, $payload);

            //Transform data
            $data = new TaskMinifiedResource(collect($response));

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Show Task By Identifier
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\ServiceRequest\GetServiceRequestRequest $request
     * @param \Modules\ServiceRequest\Services\TaskService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/servicerequest/{srhash}/task/{id}",
     *     tags={"Task"},
     *     operationId="api.backend.servicerequest.task.show",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(
     *          in="path", name="srhash", description="Enter service reqiest code or key", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          in="path", name="id", description="Enter Task identifier code", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show(GetServiceRequestRequest $request, TaskService $service, string $subdomain, ServiceRequest $servicerequest)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch ServiceRequest record
            $result = $service->show($orgHash, $payload, $servicerequest['hash']);

            //Transform data
            $data = new ServiceRequestResource($result);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Create Task
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\Task\CreateTaskRequest $request
     * @param \Modules\ServiceRequest\Services\TaskService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/servicerequest/{hash}/task",
     *     tags={"Task"},
     *     operationId="api.backend.servicerequest.task.create",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(CreateTaskRequest $request, TaskService $service, string $subdomain)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Create customer
            $data = $service->create($orgHash, $payload, $ipAddress);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Update Task
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\Task\UpdateTaskRequest $request
     * @param \Modules\ServiceRequest\Services\TaskService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/servicerequest/{hash}/task/{id}",
     *     tags={"Task"},
     *     operationId="api.backend.servicerequest.task.update",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(UpdateUserRequest $request, TaskService $service, string $subdomain, ServiceRequest $hash)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Logout customer
            $data = $service->update($orgHash, $payload, $servicerequest['hash'], $ipAddress);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Delete Task
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\Task\DeleteTaskRequest $request
     * @param \Modules\ServiceRequest\Services\TaskService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/servicerequest/{hash}/task/{id}",
     *     tags={"Task"},
     *     operationId="api.backend.servicerequest.task.delete",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy(DeleteTaskRequest $request, TaskService $service, string $subdomain, ServiceRequest $hash, int $id)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Logout customer
            $data = $service->delete($orgHash, $payload, $servicerequest['hash'], $ipAddress);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends