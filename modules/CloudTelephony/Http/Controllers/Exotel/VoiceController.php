<?php

namespace Modules\CloudTelephony\Http\Controllers\Exotel;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\CloudTelephony\Http\Requests\Exotel\VoiceCallbackRequest;
use Modules\CloudTelephony\Http\Requests\Exotel\VoiceDetailsRequest;
use Modules\CloudTelephony\Http\Requests\Exotel\VoiceCallPassthruRequest;

use Modules\CloudTelephony\Transformers\Exotel\VoiceCallbackResource;
use Modules\CloudTelephony\Transformers\Exotel\VoiceCallDetailsResource;
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
     *     path="/exotel/call/callback",
     *     tags={"Telephony"},
     *     operationId="api.telephony.exotel.call.callback",
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function callback(VoiceCallbackRequest $request, TelephonyVoiceService $service)
    {
        try {
            $payload = null;
            if (isset($request['Call'])) {
                Log::info($request['Call']);
                $payload = new VoiceCallbackResource($request);
            } else {
                throw new Exception(500);
            } //End if

            //Create payload
            $data = collect($payload);

            //Process telephony callback
            //$data = $service->callback($payload);

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
     * Save Exotel Call Details
     *
     * @param \Modules\CloudTelephony\Http\Requests\Exotel\VoiceDetailsRequest $request
     * @param \Modules\CloudTelephony\Services\TelephonyVoiceService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/exotel/call/details",
     *     tags={"Telephony"},
     *     operationId="api.telephony.exotel.call.details",
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function details(VoiceDetailsRequest $request, TelephonyVoiceService $service)
    {
        try {
            $payload = null;
            if (isset($request['Call'])) {
                $payload = new VoiceCallDetailsResource($request['Call']);
            } else {
                throw new Exception(500);
            } //End if

            //Create payload
            $data = $payload; //collect($payload);

            //Process telephony callback
            //$data = $service->create($payload);

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
     *     path="/exotel/call/passthru",
     *     tags={"Telephony"},
     *     operationId="api.telephony.exotel.call.passthru",
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
            $data = collect($payload);

            //Process telephony passthru
            //$data = $service->passthru($orgHash, $payload);

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
