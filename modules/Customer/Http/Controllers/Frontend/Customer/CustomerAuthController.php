<?php

namespace Modules\Customer\Http\Controllers\Frontend\Customer;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\Customer\Http\Requests\Frontend\Auth\CustomerExistsRequest;
use Modules\Customer\Http\Requests\Frontend\Auth\CustomerRegisterRequest;
use Modules\Customer\Http\Requests\Frontend\Auth\CustomerActivateRequest;
use Modules\Customer\Http\Requests\Frontend\Auth\CustomerLoginRequest;
use Modules\Customer\Http\Requests\Frontend\Auth\CustomerLogoutRequest;

use Modules\Customer\Services\Customer\CustomerService;
use Modules\Customer\Services\Customer\CustomerAuthService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Controller to Manage Customer Data on Frontend
 */
class CustomerAuthController extends ApiBaseController
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
     * Check if the customer exists
     *
     * @param \Modules\Api\Http\Requests\Frontend\Customer\CustomerExistsRequest $request
     * @param \Modules\Customer\Services\Customer\CustomerService $customerService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/customer/exists",
     *      tags={"Customer"},
     *      operationId="api.frontend.customer.exists",
     *      @OA\Parameter(
     *          ref="#/components/parameters/organization_key",
     *      ),
     *      @OA\Parameter(
     *          parameter="customer_phone", in="query", name="phone", description="Enter phone number w/o country code.",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          parameter="customer_email", in="query", name="email", description="Enter email address.",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function exists(CustomerExistsRequest $request, CustomerService $customerService)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Create payload
            $payload = collect($request);

            $data = $customerService->validateCustomerExists($orgHash, $payload);

            //Send http status out
            return $this->response->success(compact('data'));
        } catch(Exception $e) {

        }
    } //Function ends


    /**
     * Register New Customer
     *
     * @param \Modules\Api\Http\Requests\Frontend\Customer\CustomerRegisterRequest $request
     * @param \Modules\Customer\Services\Customer\CustomerAuthService $customerAuthService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/customer/register",
     *      tags={"Customer"},
     *      operationId="api.frontend.customer.register",
     *      @OA\Parameter(
     *          ref="#/components/parameters/organization_key",
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  required={"email", "phone", "country_idd", "password"},
     *                  @OA\Property(property="first_name", description="Enter first name.", type="string"),
     *                  @OA\Property(property="last_name", description="Enter last name.", type="string"),
     *                  @OA\Property(property="email", description="Enter email address.", type="string"),
     *                  @OA\Property(property="phone", description="Enter phone number.", type="string"),
     *                  @OA\Property(property="password", description="Enter password.", type="string"),
     *                  @OA\Property(property="country_idd", description="Enter country code.", type="string"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function register(CustomerRegisterRequest $request, CustomerAuthService $customerAuthService)
    {
        //Get Org Hash 
        $orgHash = $this->getOrgHashInRequest($request);

        //Get IP Address
        $ipAddress = $this->getIpAddressInRequest($request);

        //Create payload
        $payload = collect($request);

        //Register Customer
        $data = $customerAuthService->register($orgHash, $payload, $ipAddress);

        //Send http status out
        return $this->response->success(compact('data'));
    } //Function ends


    public function activate(CustomerActivateRequest $request, CustomerAuthService $customerAuthService, string $key)
    {
        try {
            $data = $customerAuthService->activate($request, $hash);

            //Send http status out
            return $this->response->success(compact('data'));
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Customer Login
     *
     * @param CustomerLoginRequest $request
     * @param \Modules\Customer\Services\Customer\CustomerAuthService $customerAuthService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/customer/login",
     *      tags={"Customer"},
     *      operationId="api.frontend.customer.login",
     *      @OA\Parameter(
     *          ref="#/components/parameters/organization_key",
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"username", "password"},
     *                  @OA\Property(property="username", description="Enter email address.", type="string"),
     *                  @OA\Property(property="password", description="Enter password.", type="string"),
     *                  @OA\Property(property="country_idd", description="Enter country code.", type="string"),
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
    public function login(CustomerLoginRequest $request, CustomerAuthService $customerAuthService)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create credentials object
            $credentials = collect($request->only('username', 'password'));

            //Authenticate Customer
            $data = $customerAuthService->authenticate($orgHash, $credentials, $ipAddress);

            //Send http status out
            return $this->response->success(compact('data'));
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Customer Logout or Revoke Token
     *
     * @param \Modules\Api\Http\Requests\Frontend\Customer\CustomerLogoutRequest $request
     * @param \Modules\Customer\Services\Customer\CustomerAuthService $customerAuthService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *      path="/customer/logout",
     *      tags={"Customer"},
     *      operationId="api.frontend.customer.logout",
     *      security={{"JWT_Bearer_Auth":{}}},
     *      @OA\Parameter(
     *          ref="#/components/parameters/organization_key",
     *      ),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function logout(CustomerLogoutRequest $request, CustomerAuthService $customerAuthService)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Logout customer
            $data = $customerAuthService->logout($orgHash, $payload, $ipAddress);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends
