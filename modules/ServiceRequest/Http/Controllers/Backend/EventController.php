<?php

namespace Modules\ServiceRequest\Http\Controllers\Backend;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\ServiceRequest\Models\Event;

use Modules\Core\Http\Controllers\ApiBaseController;

use Modules\ServiceRequest\Http\Requests\Backend\Event\FetchEventRequest;
use Modules\ServiceRequest\Http\Requests\Backend\Event\CreateEventRequest;
use Modules\ServiceRequest\Http\Requests\Backend\Event\UpdateEventRequest;
use Modules\ServiceRequest\Http\Requests\Backend\Event\DeleteEventRequest;

use Modules\ServiceRequest\Transformers\Responses\EventResource;
use Modules\ServiceRequest\Transformers\Responses\EventMinifiedResource;

use Modules\ServiceRequest\Services\EventService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class EventController extends ApiBaseController
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
     * Get All Events for an Organization
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\Event\FetchEventRequest $request
     * @param \Modules\ServiceRequest\Services\EventService $service
     * @param \string $subdomain
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/event/fetch",
     *     tags={"Event"},
     *     operationId="api.backend.servicerequest.event.index",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(FetchEventRequest $request, EventService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch data
            $response = $service->getAll($orgHash, $payload);

            //Transform data
            $data = new EventMinifiedResource(collect($response));

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Show Event By Identifier
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\ServiceRequest\GetServiceRequestRequest $request
     * @param \Modules\ServiceRequest\Services\EventService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/event/{id}",
     *     tags={"Event"},
     *     operationId="api.backend.servicerequest.event.show",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(
     *          in="path", name="srhash", description="Enter service reqiest code or key", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          in="path", name="id", description="Enter Event identifier code", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show(FetchEventRequest $request, EventService $service, string $subdomain, ServiceRequest $servicerequest)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch individual record
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
     * Create Event
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\Event\CreateEventRequest $request
     * @param \Modules\ServiceRequest\Services\EventService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/event",
     *     tags={"Event"},
     *     operationId="api.backend.servicerequest.event.create",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(CreateEventRequest $request, EventService $service, string $subdomain)
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
     * Update Event
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\Event\UpdateEventRequest $request
     * @param \Modules\ServiceRequest\Services\EventService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/servicerequest/{hash}/event/{id}",
     *     tags={"Event"},
     *     operationId="api.backend.servicerequest.event.update",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(UpdateUserRequest $request, EventService $service, string $subdomain, ServiceRequest $hash)
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
     * Delete Event
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\Event\DeleteEventRequest $request
     * @param \Modules\ServiceRequest\Services\EventService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/servicerequest/{hash}/event/{id}",
     *     tags={"Event"},
     *     operationId="api.backend.servicerequest.event.delete",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy(DeleteEventRequest $request, EventService $service, string $subdomain, ServiceRequest $hash, int $id)
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
