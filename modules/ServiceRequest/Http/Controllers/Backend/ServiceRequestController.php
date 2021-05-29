<?php

namespace Modules\ServiceRequest\Http\Controllers\Backend;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\ServiceRequest\Models\ServiceRequest;

use Modules\Core\Http\Controllers\ApiBaseController;

use Modules\ServiceRequest\Http\Requests\Backend\ServiceRequest\GetServiceRequestRequest;
use Modules\ServiceRequest\Http\Requests\Backend\ServiceRequest\CreateServiceRequestRequest;
use Modules\ServiceRequest\Http\Requests\Backend\ServiceRequest\UpdateServiceRequestRequest;
use Modules\ServiceRequest\Http\Requests\Backend\ServiceRequest\DeleteServiceRequestRequest;

use Modules\ServiceRequest\Transformers\Responses\ServiceRequestResource;
use Modules\ServiceRequest\Transformers\Responses\ServiceRequestMinifiedResource;

use Modules\ServiceRequest\Services\ServiceRequestService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ServiceRequestController extends ApiBaseController
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
     * Get All Service Request for an Organization
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\ServiceRequest\GetServiceRequestRequest $request
     * @param \Modules\ServiceRequest\Services\ServiceRequestService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/servicerequest/fetch",
     *     tags={"ServiceRequest"},
     *     operationId="api.backend.servicerequest.index",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(GetServiceRequestRequest $request, ServiceRequestService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch all data for service request by Organization
            $response = $service->getFullData($orgHash, $payload);

            //Transform data
            $data = new ServiceRequestMinifiedResource(collect($response));

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Show ServiceRequest By Identifier
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\ServiceRequest\GetServiceRequestRequest $request
     * @param \Modules\ServiceRequest\Services\ServiceRequestService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/servicerequest/{hash}",
     *     tags={"ServiceRequest"},
     *     operationId="api.backend.servicerequest.show",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(
     *          in="path", name="ohash", description="Enter organization code or key", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show(GetServiceRequestRequest $request, ServiceRequestService $service, string $subdomain, ServiceRequest $servicerequest)
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

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Create ServiceRequest
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\ServiceRequest\CreateServiceRequestRequest $request
     * @param \Modules\ServiceRequest\Services\ServiceRequestService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/servicerequest",
     *     tags={"ServiceRequest"},
     *     operationId="api.backend.servicerequest.create",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(CreateServiceRequestRequest $request, ServiceRequestService $service, string $subdomain)
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

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Update ServiceRequest
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\ServiceRequest\UpdateServiceRequestRequest $request
     * @param \Modules\ServiceRequest\Services\ServiceRequestService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/servicerequest/{hash}",
     *     tags={"ServiceRequest"},
     *     operationId="api.backend.servicerequest.update",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(UpdateServiceRequestRequest $request, ServiceRequestService $service, string $subdomain, ServiceRequest $servicerequest)
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

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Delete ServiceRequest
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\ServiceRequest\DeleteServiceRequestRequest $request
     * @param \Modules\ServiceRequest\Services\ServiceRequestService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/servicerequest/{hash}",
     *     tags={"ServiceRequest"},
     *     operationId="api.backend.servicerequest.delete",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy(DeleteServiceRequestRequest $request, ServiceRequestService $service, string $subdomain, ServiceRequest $servicerequest)
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

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends
