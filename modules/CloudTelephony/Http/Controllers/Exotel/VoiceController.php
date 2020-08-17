<?php

namespace Modules\CloudTelephony\Http\Controllers\Exotel;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\CloudTelephony\Http\Requests\Exotel\VoiceCallbackRequest;
use Modules\CloudTelephony\Http\Requests\Exotel\VoiceDetailsRequest;
use Modules\CloudTelephony\Http\Requests\Exotel\VoiceCallPassthruRequest;

use Modules\CloudTelephony\Transformers\Exotel\VoiceCallbackResource;
use Modules\CloudTelephony\Transformers\Exotel\VoiceCallPassthruResource;

use Modules\CloudTelephony\Services\TelephonyVoiceService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class VoiceController extends ApiBaseController
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
     * Telephony Exotel Callback
     *
     * @param \Modules\CloudTelephony\Http\Requests\Exotel\VoiceCallbackRequest $request
     * @param \Modules\CloudTelephony\Services\TelephonyVoiceService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/telephony/exotel/call/callback",
     *     tags={"Telephony"},
     *     operationId="api.telephony.exotel.call.callback",
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function callback(VoiceCallbackRequest $request, TelephonyVoiceService $service)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            $payload = null;
            if (isset($request['CallSid'])) {
                $payload = new VoiceCallbackResource($request);
            } else {
                throw new Exception(500);
            } //End if

            //Create payload
            $payload = collect($payload);

            //Process telephony callback
            $data = $service->callback($orgHash, 'exotel', $payload);

            //Send http status out
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
     * Telephony Exotel Passthru
     *
     * @param \Modules\CloudTelephony\Http\Requests\Exotel\VoiceCallPassthruRequest $request
     * @param \Modules\CloudTelephony\Services\TelephonyVoiceService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/telephony/exotel/call/passthru",
     *     tags={"Telephony"},
     *     operationId="api.telephony.exotel.call.passthru",
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function passthru(VoiceCallPassthruRequest $request, TelephonyVoiceService $service)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            $payload = null;
            if (isset($request['CallSid'])) {
                $payload = new VoiceCallPassthruResource($request);
            } else {
                throw new Exception(500);
            } //End if

            //Create payload
            $payload = collect($payload);

            //Process telephony details
            $data = $service->details($orgHash, 'exotel', $payload);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(UnauthorizedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    public function test(VoiceCallPassthruRequest $request, TelephonyVoiceService $service) {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Create payload
            $payload = collect($request);

            //Process telephony details
            $data = $service->makecall($orgHash, $payload);

            //Send http status out
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
