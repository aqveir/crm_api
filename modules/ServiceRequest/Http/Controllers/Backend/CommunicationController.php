<?php

namespace Modules\ServiceRequest\Http\Controllers\Backend;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\ServiceRequest\Models\ServiceRequestCommunication as Communication;
use Modules\ServiceRequest\Models\ServiceRequest;

use Modules\Core\Http\Controllers\ApiBaseController;

use Modules\ServiceRequest\Http\Requests\Backend\Communication\FetchCommunicationRequest;
use Modules\ServiceRequest\Http\Requests\Backend\Communication\CallCommunicationRequest;
use Modules\ServiceRequest\Http\Requests\Backend\Communication\SmsSendCommunicationRequest;
use Modules\ServiceRequest\Http\Requests\Backend\Communication\MailSendCommunicationRequest;

use Modules\ServiceRequest\Transformers\Responses\TaskResource;
use Modules\ServiceRequest\Transformers\Responses\CommunicationMinifiedResource;

use Modules\ServiceRequest\Services\CommunicationService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CommunicationController extends ApiBaseController
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
     * Get All Communicatons for a ServiceRequest
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\Communication\FetchTaskRequest $request
     * @param \Modules\ServiceRequest\Services\CommunicationService $service
     * @param \string $subdomain
     * @param \Modules\ServiceRequest\Models\ServiceRequest $servicerequest
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/servicerequest/{hash}/communication",
     *     tags={"Communication"},
     *     operationId="api.backend.servicerequest.communication.index",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(FetchTaskRequest $request, CommunicationService $service, string $subdomain, ServiceRequest $servicerequest)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch Users data for Organization
            $response = $service->getAll($orgHash, $payload, $servicerequest['hash']);

            //Transform data
            $data = new CommunicationMinifiedResource(collect($response));

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Show Communication By Identifier
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\ServiceRequest\FetchTaskRequest $request
     * @param \Modules\ServiceRequest\Services\CommunicationService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/servicerequest/{srhash}/task/{id}",
     *     tags={"Communication"},
     *     operationId="api.backend.servicerequest.communication.show",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(
     *          in="path", name="srhash", description="Enter service reqiest code or key", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          in="path", name="id", description="Enter Communication identifier code", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show(FetchTaskRequest $request, CommunicationService $service, string $subdomain, ServiceRequest $servicerequest)
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
     * Send SMS Communication
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\Communication\SmsSendCommunicationRequest $request
     * @param \Modules\ServiceRequest\Services\CommunicationService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/servicerequest/{hash}/sms",
     *     tags={"Communication"},
     *     operationId="api.backend.servicerequest.communication.sms.send",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function sms(SmsSendCommunicationRequest $request, CommunicationService $service, string $subdomain, ServiceRequest $servicerequest)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Call service to send sms
            $data = $service->sendSMS($orgHash, $servicerequest['hash'], $payload, $ipAddress);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Send Mail Communication
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\Communication\MailSendCommunicationRequest $request
     * @param \Modules\ServiceRequest\Services\CommunicationService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/servicerequest/{hash}/mail",
     *     tags={"Communication"},
     *     operationId="api.backend.servicerequest.communication.mail.send",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function mail(MailSendCommunicationRequest $request, CommunicationService $service, string $subdomain, ServiceRequest $servicerequest)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Call service to send mail
            $data = $service->sendMail($orgHash, $servicerequest['hash'], $payload, $ipAddress);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Update Communication
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\Communication\UpdateTaskRequest $request
     * @param \Modules\ServiceRequest\Services\CommunicationService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/servicerequest/{hash}/comm/{id}",
     *     tags={"Communication"},
     *     operationId="api.backend.servicerequest.communication.update",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(UpdateUserRequest $request, CommunicationService $service, string $subdomain, ServiceRequest $hash)
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
     * Delete Communication
     *
     * @param \Modules\ServiceRequest\Http\Requests\Backend\Communication\DeleteTaskRequest $request
     * @param \Modules\ServiceRequest\Services\CommunicationService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/servicerequest/{hash}/comm/{id}",
     *     tags={"Communication"},
     *     operationId="api.backend.servicerequest.communication.delete",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy(DeleteTaskRequest $request, CommunicationService $service, string $subdomain, ServiceRequest $hash, int $id)
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
