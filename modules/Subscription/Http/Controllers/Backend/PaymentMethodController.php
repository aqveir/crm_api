<?php

namespace Modules\Subscription\Http\Controllers\Backend;

use Config;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\Subscription\Http\Requests\Backend\CreatePaymentMethodRequest;
use Modules\Subscription\Http\Requests\Backend\UpdateSubscriptionRequest;
use Modules\Subscription\Http\Requests\Backend\DeleteSubscriptionRequest;

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
        //$this->authorizeResource(Subscription::class, 'subscription');
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
     *      path="/organization/paymentmethods/fetch",
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
     * Create Subscription
     *
     * @param \Modules\Subscription\Http\Requests\Backend\CreatePaymentMethodRequest $request
     * @param \Modules\Subscription\Services\PaymentMethodService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/organization/paymentmethods",
     *     tags={"Organization"},
     *     operationId="api.backend.subscription.paymentmethod.create",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(CreatePaymentMethodRequest $request, PaymentMethodService $service, string $subdomain)
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
     * @param \Modules\Subscription\Services\PaymentMethodService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/organization/paymentmethods/{id}",
     *     tags={"Organization"},
     *     operationId="api.backend.subscription.paymentmethod.update",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(UpdateSubscriptionRequest $request, PaymentMethodService $service, string $subdomain, Subscription $subscription)
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
     * @param \Modules\Subscription\Services\PaymentMethodService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/organization/paymentmethods/{id}",
     *     tags={"Organization"},
     *     operationId="api.backend.subscription.paymentmethod.delete",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy(DeleteSubscriptionRequest $request, PaymentMethodService $service, string $subdomain, Subscription $subscription)
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
