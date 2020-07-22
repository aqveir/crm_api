<?php

namespace Modules\User\Http\Controllers\Backend\User;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\User\Http\Requests\Backend\User\CreateUserRequest;
use Modules\User\Http\Requests\Backend\User\UpdateUserRequest;

use Modules\User\Services\User\UserService;
use Modules\User\Services\User\UserAuthService;

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
     * Create User
     *
     * @param \Modules\User\Http\Requests\Backend\User\CreateUserRequest $request
     * @param \Modules\User\Services\User\UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/user",
     *     tags={"User"},
     *     operationId="api.backend.user.create",
     *     security={{"JWT_Bearer_Auth":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(CreateUserRequest $request, UserService $userService)
    {   
        try {
            //Authenticate
            $user=$userService->createUser($request, 1);

            //Send the JSON response
            return response()
                ->json([], config('omnichannel.settings.http_status_code.success'));
        } catch(Exception $e) {

        } //Try-catch ends

    } //Function ends



    /**
     * Update User
     *
     * @param \Modules\User\Http\Requests\Backend\User\UpdateUserRequest $request
     * @param \Modules\User\Services\User\UserService $userService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/user/{hash}",
     *     tags={"User"},
     *     operationId="api.backend.user.update",
     *     security={{"JWT_Bearer_Auth":{}}},
     *     @OA\Parameter(
     *          parameter="hash", in="path", name="hash", description="Enter user identifier.",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(UpdateUserRequest $request, UserService $userService)
    {   
        try {
            //Authenticate
            $user=$userService->createUser($request, 1);

            //Send the JSON response
            return response()
                ->json([], config('omnichannel.settings.http_status_code.success'));
        } catch(Exception $e) {

        } //Try-catch ends

    } //Function ends


} //Class ends
