<?php

namespace Modules\Contact\Http\Controllers\Frontend\Contact;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\Contact\Http\Requests\Frontend\Auth\ContactExistsRequest;
use Modules\Contact\Http\Requests\Frontend\Auth\ContactRegisterRequest;
use Modules\Contact\Http\Requests\Frontend\Auth\ContactActivateRequest;
use Modules\Contact\Http\Requests\Frontend\Auth\ContactLoginRequest;
use Modules\Contact\Http\Requests\Frontend\Auth\ContactLogoutRequest;

use Modules\Contact\Services\Contact\ContactService;
use Modules\Contact\Services\Contact\ContactAuthService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Controller to Manage Contact Data on Frontend
 */
class ContactAuthController extends ApiBaseController
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
     * Check if the contact exists
     *
     * @param \Modules\Api\Http\Requests\Frontend\Contact\ContactExistsRequest $request
     * @param \Modules\Contact\Services\Contact\ContactService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/contact/exists",
     *      tags={"Contact"},
     *      operationId="api.frontend.contact.exists",
     *      @OA\Parameter(
     *          ref="#/components/parameters/organization_key",
     *      ),
     *      @OA\Parameter(
     *          in="query", name="phone", description="Enter phone number w/o country code.", required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          in="query", name="email", description="Enter email address.", required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function exists(ContactExistsRequest $request, ContactService $service)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Create payload
            $payload = collect($request);

            $data = $service->exists($orgHash, $payload);

            //Send response data
            return $this->response->success(compact('data'));
        } catch(Exception $e) {

        }
    } //Function ends


    /**
     * Register New Contact
     *
     * @param \Modules\Api\Http\Requests\Frontend\Contact\ContactRegisterRequest $request
     * @param \Modules\Contact\Services\Contact\ContactAuthService $customerAuthService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/contact/register",
     *      tags={"Contact"},
     *      operationId="api.frontend.contact.register",
     *      @OA\Parameter(
     *          ref="#/components/parameters/organization_key",
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  required={"email", "phone", "password"},
     *                  @OA\Property(property="first_name", description="Enter first name.", type="string"),
     *                  @OA\Property(property="last_name", description="Enter last name.", type="string"),
     *                  @OA\Property(property="email", description="Enter email address.", type="string"),
     *                  @OA\Property(property="phone", description="Enter phone number (E164 format).", type="string"),
     *                  @OA\Property(property="password", description="Enter password.", type="string")
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function register(ContactRegisterRequest $request, ContactAuthService $customerAuthService)
    {
        //Get Org Hash 
        $orgHash = $this->getOrgHashInRequest($request);

        //Get IP Address
        $ipAddress = $this->getIpAddressInRequest($request);

        //Create payload
        $payload = collect($request);

        //Register Contact
        $data = $customerAuthService->register($orgHash, $payload, $ipAddress);

        //Send response data
        return $this->response->success(compact('data'));
    } //Function ends


    public function activate(ContactActivateRequest $request, ContactAuthService $customerAuthService, string $key)
    {
        try {
            $data = $customerAuthService->activate($request, $hash);

            //Send response data
            return $this->response->success(compact('data'));
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Contact Login
     *
     * @param ContactLoginRequest $request
     * @param \Modules\Contact\Services\Contact\ContactAuthService $customerAuthService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/contact/login",
     *      tags={"Contact"},
     *      operationId="api.frontend.contact.login",
     *      @OA\Parameter(
     *          ref="#/components/parameters/organization_key",
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"username", "password"},
     *                  @OA\Property(property="username", description="Enter email address.", type="string"),
     *                  @OA\Property(property="password", description="Enter password.", type="string")
     *              ),
     *          ),
     *      ),
     *      @OA\RequestBody(required=true, @OA\JsonContent(
     *          @OA\Property(title="Name", description="Name of the new project", example="A nice project")
     *      )),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function login(ContactLoginRequest $request, ContactAuthService $customerAuthService)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create credentials object
            $credentials = collect($request->only('username', 'password'));

            //Authenticate Contact
            $data = $customerAuthService->authenticate($orgHash, $credentials, $ipAddress);

            //Send response data
            return $this->response->success(compact('data'));
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Contact Logout or Revoke Token
     *
     * @param \Modules\Api\Http\Requests\Frontend\Contact\ContactLogoutRequest $request
     * @param \Modules\Contact\Services\Contact\ContactAuthService $customerAuthService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *      path="/contact/logout",
     *      tags={"Contact"},
     *      operationId="api.frontend.contact.logout",
     *      security={{"omni_token":{}}},
     *      @OA\Parameter(
     *          ref="#/components/parameters/organization_key",
     *      ),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function logout(ContactLogoutRequest $request, ContactAuthService $customerAuthService)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Logout contact
            $data = $customerAuthService->logout($orgHash, $payload, $ipAddress);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends
