<?php

namespace Modules\User\Http\Controllers\Backend\User;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\User\Http\Requests\Backend\User\SaveUserAvailabilityRequest;

use Modules\User\Services\User\UserAvailabilityService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Modules\Core\Exceptions\ExistingDataException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SetUserAvailabilityController extends ApiBaseController
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
     * Set User Availability Status
     *
     * @param \Modules\User\Http\Requests\Backend\User\SaveUserAvailabilityRequest $request
     * @param \Modules\User\Services\User\UserAvailabilityService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/user/status/{key}",
     *     tags={"User"},
     *     operationId="api.backend.user.availability.status",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(
     *          in="path", name="key", description="Enter user availability status (i.e. online, away)", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(SaveUserAvailabilityRequest $request, UserAvailabilityService $service, string $key)
    {   
        try {
            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Set Status
            $data = $service->update($payload, $key, $ipAddress);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(ExistingDataException $e) {
            return $this->response->fail(['EXCEPTION_EXISTING_DATA'], Response::HTTP_BAD_REQUEST);
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends
