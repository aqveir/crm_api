<?php

namespace Modules\Contact\Http\Controllers\Backend\Contact;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\Contact\Http\Requests\Backend\Contact\TelephonyRequest;

use Modules\Contact\Services\Contact\ContactTelephonyService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Controller to Manage Contact Telephony on Backend
 */
class TelephonyController extends ApiBaseController
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
     * Make a Telephony Call to Contact using Default Preferred Number
     *
     * @param \Modules\Api\Http\Requests\Backend\Contact\TelephonyRequest $request
     * @param \Modules\Contact\Services\Contact\ContactTelephonyService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/contact/{hash}/call",
     *      tags={"Contact"},
     *      operationId="api.backend.contact.call",
     *      security={{"omni_token":{}}},
     *      @OA\Parameter(ref="#/components/parameters/organization_key"),
     *      @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function call(TelephonyRequest $request, ContactTelephonyService $service, string $subdomain, string $hash)
    {
        return $this->callToProxy($request, $service, $subdomain, $hash);
    } //Function ends



    /**
     * Make a Telephony Call to Contact using Proxy Number
     *
     * @param \Modules\Api\Http\Requests\Backend\Contact\TelephonyRequest $request
     * @param \Modules\Contact\Services\Contact\ContactTelephonyService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/contact/{hash}/call/{proxy}",
     *      tags={"Contact"},
     *      operationId="api.backend.contact.call.proxy",
     *      security={{"omni_token":{}}},
     *      @OA\Parameter(ref="#/components/parameters/organization_key"),
     *      @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function callToProxy(TelephonyRequest $request, ContactTelephonyService $service, string $subdomain, string $hash, string $proxy=null)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Call the 
            $data = $service->makeCall($orgHash, $hash, $proxy, $payload, $ipAddress);

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
