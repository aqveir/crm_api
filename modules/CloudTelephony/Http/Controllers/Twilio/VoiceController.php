<?php

namespace Modules\CloudTelephony\Http\Controllers\Twilio;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Modules\CloudTelephony\Http\Controllers\BaseVoiceController;

use Modules\CloudTelephony\Services\TelephonyVoiceService;

use Modules\CloudTelephony\Http\Requests\Twilio\VoiceCallbackRequest;
use Modules\CloudTelephony\Http\Requests\Twilio\VoiceDetailsRequest;
use Modules\CloudTelephony\Http\Requests\Twilio\VoiceCallPassthruRequest;

use Modules\CloudTelephony\Transformers\Twilio\VoiceCallbackResource as TwilioVoiceCallbackResource;
use Modules\CloudTelephony\Transformers\Twilio\VoiceCallPassthruResource as TwilioVoiceCallPassthruResource;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class VoiceController extends BaseVoiceController
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
     * Telephony Twilio Callback
     *
     * @param \Modules\CloudTelephony\Http\Requests\Twilio\VoiceCallbackRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/telephony/twilio/callback",
     *     tags={"Telephony"},
     *     operationId="api.telephony.twilio.call.callback",
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function callback(VoiceCallbackRequest $request, TelephonyVoiceService $service, string $subdomain)
    {
        try {
            //Provider switchcase to transform request
            $payload = null;
            if (!isset($request['CallSid'])) { throw new BadRequestHttpException('Twilio: Missing CallSid'); } //End if
            $payload = new ExotelVoiceCallbackResource($request);

            //Create payload
            $payload = collect($payload);

            //Process telephony callback
            return parent::processCallback($request, $service, $subdomain, 'twilio', $payload);
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(UnauthorizedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Telephony Twilio Passthru
     *
     * @param \Modules\CloudTelephony\Http\Requests\Twilio\VoiceCallPassthruRequest $request
     * @param \Modules\CloudTelephony\Services\TelephonyVoiceService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/telephony/twilio/passthru",
     *     tags={"Telephony"},
     *     operationId="api.telephony.twilio.call.passthru",
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function passthru(VoiceCallPassthruRequest $request, TelephonyVoiceService $service, string $subdomain)
    {
        try {
            //Provider switchcase to transform request
            $payload = null;
            if (!isset($request['CallSid'])) { throw new BadRequestHttpException('Twilio: Missing CallSid'); } //End if
            $payload = new ExotelVoiceCallPassthruResource($request);

            //Create payload
            $payload = collect($payload);

            //Process telephony callback
            return parent::processPassthru($request, $service, $subdomain, 'twilio', $payload);
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(UnauthorizedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    public function test(VoiceCallPassthruRequest $request, TelephonyVoiceService $service, string $subdomain) {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Create payload
            $payload = collect($request);

            //Process telephony details
            $data = $service->makecall($orgHash, $payload);

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
