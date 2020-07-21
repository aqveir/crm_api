<?php

namespace Modules\Customer\Http\Controllers\Backend\Customer;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\Customer\Http\Requests\Backend\Customer\GetAllCustomerRequest;

use Modules\Customer\Services\Customer\CustomerService;

use Exception;
use Modules\Core\Exceptions\DuplicateDataException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Controller to Manage Customer Data on Backend
 */
class GetCustomerController extends ApiBaseController
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
     * Get All Customers (Backend)
     *
     * @param \Modules\Api\Http\Requests\Frontend\Customer\GetAllCustomerRequest $request
     * @param \Modules\Customer\Services\Customer\CustomerService $customerService
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/customer",
     *      tags={"Customer"},
     *      operationId="api.backend.customer.all",
     *      security={{"JWT_Bearer_Auth":{}}},
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(GetAllCustomerRequest $request, CustomerService $customerService)
    {   
        try {
            //Get collection of all customers
            $data=$customerService->getCustomerData($request, 1);

            //Send http status out
            return $this->response->success(compact('data'));
        } catch (AccessDeniedHttpException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        } catch (UnauthorizedHttpException $e) {
            throw new UnauthorizedHttpException($e->getMessage());
        } catch (Exception $e) {  
            throw new HttpException(500, $e->getMessage());
        } //Try-catch ends

    } //Function ends


    /**
     * Get Customer By Identifier (Backend)
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Customer\Services\Customer\CustomerService $customerService
     * @param \string $hash
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/customer/{hash}",
     *      tags={"Customer"},
     *      operationId="api.backend.customer.data",
     *      security={{"JWT_Bearer_Auth":{}}},
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function data(Request $request, CustomerService $customerService, string $hash)
    {   
        try {
            //Get data of the Customer
            $data=$customerService->getFullData($request, $hash);

            //Send http status out
            return $this->response->success(compact('data'));
        } catch (AccessDeniedHttpException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        } catch (UnauthorizedHttpException $e) {
            throw new UnauthorizedHttpException($e->getMessage());
        } catch (Exception $e) {  
            throw new HttpException(500, $e->getMessage());
        } //Try-catch ends

    } //Function ends

} //Class ends
