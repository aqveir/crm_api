<?php

namespace Modules\Subscription\Http\Controllers\Backend;

use Config;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\ApiBaseController;

use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Services\PlanService;

use Modules\Subscription\Transformers\Responses\PlanMinifiedResource;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class PlanController extends ApiBaseController
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
     * Create Subscription Plans/Pricing
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Subscription\Services\PlanService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/subscription/plan",
     *      tags={"Subscription"},
     *      operationId="api.backend.subscription.plan.create",
     *      security={{"omni_token":{}}},
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(Request $request, PlanService $service, string $subdomain)
    {
        try {
            //Create payload
            $payload = collect($request);

            //Fetch Subscriptions
            $data = $service->create($payload);

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
     * Update Subscription Plans/Pricing
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Subscription\Services\PlanService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *      path="/subscription/plan",
     *      tags={"Subscription"},
     *      operationId="api.backend.subscription.plan.update",
     *      security={{"omni_token":{}}},
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(Request $request, PlanService $service, string $subdomain)
    {
        try {
            //Create payload
            $payload = collect($request);

            //Fetch Subscriptions
            $data = $service->updateAll($payload);

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

} //Class ends
