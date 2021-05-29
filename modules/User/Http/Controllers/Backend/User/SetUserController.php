<?php

namespace Modules\User\Http\Controllers\Backend\User;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\User\Http\Requests\Backend\User\RegisterUserRequest;
use Modules\User\Http\Requests\Backend\User\CreateUserRequest;
use Modules\User\Http\Requests\Backend\User\UpdateUserRequest;
use Modules\User\Http\Requests\Backend\User\UserVerifyRequest;
use Modules\User\Http\Requests\Backend\User\UserActivateRequest;

use Modules\User\Services\UserService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SetUserController extends ApiBaseController
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
     * Register User
     *
     * @param \Modules\User\Http\Requests\Backend\User\RegisterUserRequest $request
     * @param \Modules\User\Services\UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/user/register",
     *     tags={"User"},
     *     operationId="api.backend.user.register",
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function register(RegisterUserRequest $request, UserService $userService)
    {   
        try {
            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Create customer
            $data = $userService->register($payload, $ipAddress);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


} //Class ends
