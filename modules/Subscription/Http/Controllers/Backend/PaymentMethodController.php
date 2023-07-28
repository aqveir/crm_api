<?php

namespace Modules\Subscription\Http\Controllers\Backend;

use Config;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\Subscription\Http\Requests\Backend\UpdateSubscriptionRequest;

use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Services\PaymentMethodService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class PaymentMethodController extends ApiBaseController
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
     * Get All Organization Payment Methods
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Subscription\Services\PaymentMethodService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/organization/paymentmethod/fetch",
     *      tags={"Organization"},
     *      operationId="api.backend.subscription.paymentmethod.index",
     *      security={{"omni_token":{}}},
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(Request $request, PaymentMethodService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch Subscriptions
            $data = $service->index($orgHash, $payload);

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
     * Get Setup Intent for the Organization
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Subscription\Services\PaymentMethodService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/organization/paymentmethod/intent",
     *      tags={"Organization"},
     *      operationId="api.backend.subscription.paymentmethod.intent",
     *      security={{"omni_token":{}}},
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function intent(Request $request, PaymentMethodService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch Subscriptions
            $data = $service->setupIntent($orgHash, $payload);

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
     * Create Payment Method for Organization
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Subscription\Services\PaymentMethodService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/organization/paymentmethod",
     *     tags={"Organization"},
     *     operationId="api.backend.subscription.paymentmethod.create",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(Request $request, PaymentMethodService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Create subscription data
            $data = $service->create($orgHash, $payload, ($payload->has('is_default')?$payload['is_default']:false));

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
     * Update Payment Method
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Subscription\Services\PaymentMethodService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/organization/paymentmethod/{uuid}",
     *     tags={"Organization"},
     *     operationId="api.backend.subscription.paymentmethod.update",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(Request $request, PaymentMethodService $service, string $subdomain, string $uuid)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Update Payment Method
            $data = $service->update($orgHash, $payload, $uuid);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Delete Payment Method
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Subscription\Services\PaymentMethodService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/organization/paymentmethod/{uuid}",
     *     tags={"Organization"},
     *     operationId="api.backend.subscription.paymentmethod.delete",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy(Request $request, PaymentMethodService $service, string $subdomain, string $uuid)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Delete Payment Method
            $data = $service->delete($orgHash, $payload, $uuid);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends
