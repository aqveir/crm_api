<?php

namespace Modules\Subscription\Http\Controllers\Frontend;

use Config;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\ApiBaseController;

use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Services\SubscriptionService;

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
    }


    /**
     * Get All Active Subscriptions
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Subscription\Services\SubscriptionService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/subscription/active",
     *      tags={"Subscription"},
     *      operationId="api.backend.subscription.active",
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(Request $request, SubscriptionService $service, string $subdomain)
    {
        try {
            //Create payload
            $payload = collect($request);

            //Fetch Subscription that is active and display filtered
            $data = $service->index($payload, true, true);

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
