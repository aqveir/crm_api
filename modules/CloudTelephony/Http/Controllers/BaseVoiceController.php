<?php

namespace Modules\CloudTelephony\Http\Controllers;

use Config;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;

use Modules\CloudTelephony\Services\TelephonyVoiceService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

abstract class BaseVoiceController extends ApiBaseController
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
     * Telephony Callback - Base Method
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Modules\CloudTelephony\Services\TelephonyVoiceService  $service
     * @param  \string  $subdomain
     * @param  \string  $provider
     * @param  \Illuminate\Support\Collection  $payload
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     */
    protected function processCallback(Request $request, TelephonyVoiceService $service, string $subdomain, string $provider, Collection $payload)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Check Provider exists
            if (empty($payload)) { throw new BadRequestHttpException('Telephony: Missing Data'); }

            //Process telephony callback
            $data = $service->callback($orgHash, $provider, $payload, $ipAddress);

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
     * Telephony Passthru - Base Method
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Modules\CloudTelephony\Services\TelephonyVoiceService  $service
     * @param  \string  $subdomain
     * @param  \string  $provider
     * @param  \Illuminate\Support\Collection  $payload
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function processPassthru(Request $request, TelephonyVoiceService $service, string $subdomain, string $provider, Collection $payload)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Check Provider exists
            if (empty($payload)) { throw new BadRequestHttpException('Telephony: Missing Data'); }

            //Process telephony details
            return $service->details($orgHash, $provider, $payload, $ipAddress);

        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(UnauthorizedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends
