<?php

namespace Modules\Subscription\Http\Controllers\Backend;

use Config;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\Subscription\Http\Requests\Backend\CreateSubscriptionRequest;
use Modules\Subscription\Http\Requests\Backend\UpdateSubscriptionRequest;
use Modules\Subscription\Http\Requests\Backend\DeleteSubscriptionRequest;

use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Services\SubscriptionService;

use Modules\Subscription\Transformers\Responses\SubscriptionMinifiedResource;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SubscriptionController extends ApiBaseController
{
    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();
        //$this->authorizeResource(Subscription::class, 'subscription');
    }


    /**
     * Get All Subscriptions
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Subscription\Services\SubscriptionService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/subscription",
     *      tags={"Subscription"},
     *      operationId="api.backend.subscription.all",
     *      security={{"omni_token":{}}},
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(Request $request, SubscriptionService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch Subscriptions
            $response = $service->index($orgHash, $payload);

            //Transform data
            $data = new SubscriptionMinifiedResource($response);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(UnauthorizedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Get Subscriptions Details by Key
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Subscription\Services\SubscriptionService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/subscription/{uuid}",
     *      tags={"Subscription"},
     *      operationId="api.backend.subscription.show",
     *      security={{"omni_token":{}}},
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show(Request $request, SubscriptionService $service, string $subdomain, string $uuid)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch Subscriptions
            $data = $service->show($orgHash, $payload, $uuid);

            //Transform data
            //$data = new SubscriptionMinifiedResource($response);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(UnauthorizedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Create Subscription
     *
     * @param \Modules\Subscription\Http\Requests\Backend\CreateSubscriptionRequest $request
     * @param \Modules\Subscription\Services\SubscriptionService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/subscription",
     *     tags={"Subscription"},
     *     operationId="api.backend.subscription.create",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(CreateSubscriptionRequest $request, SubscriptionService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Create subscription data
            $data = $service->create($orgHash, $payload);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(UnauthorizedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Update Subscription
     *
     * @param \Modules\Subscription\Http\Requests\Backend\UpdateSubscriptionRequest $request
     * @param \Modules\Subscription\Services\SubscriptionService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/subscription/{id}",
     *     tags={"Subscription"},
     *     operationId="api.backend.subscription.update",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(UpdateSubscriptionRequest $request, SubscriptionService $service, string $subdomain, Subscription $subscription)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Update subscription
            $data = $service->update($payload, $subscription['id']);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Delete Subscription
     *
     * @param \Modules\Subscription\Http\Requests\Backend\DeleteSubscriptionRequest $request
     * @param \Modules\Subscription\Services\SubscriptionService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/subscription/{id}",
     *     tags={"Subscription"},
     *     operationId="api.backend.subscription.delete",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy(DeleteSubscriptionRequest $request, SubscriptionService $service, string $subdomain, Subscription $subscription)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Delete subscription
            $data = $service->delete($payload, $subscription['id']);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends
