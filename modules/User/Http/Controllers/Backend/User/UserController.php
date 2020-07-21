<?php

namespace Modules\User\Http\Controllers\Backend\User;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\Api\Http\Requests\Backend\User\CreateUserRequest;
use Modules\Api\Http\Requests\Backend\User\UpdateUserRequest;

use Modules\Core\Services\User\UserService;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserController extends ApiBaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    /**
     * Create New User
     *
     * @param CreateUserRequest $request
     * @param \Modules\Core\Services\User\UserService $userService
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/user",
     *     tags={"User"},
     *     operationId="api.user.create",
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
