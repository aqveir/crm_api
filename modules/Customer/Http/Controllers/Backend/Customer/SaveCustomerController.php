<?php

namespace Modules\Customer\Http\Controllers\Backend\Customer;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\Api\Http\Requests\Backend\Customer\CreateCustomerRequest;
use Modules\Api\Http\Requests\Backend\Customer\UpdateCustomerRequest;

use Modules\Customer\Services\Customer\CustomerService;

use Exception;
use Modules\Core\Exceptions\DuplicateDataException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SaveCustomerController extends ApiBaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('backend');
    }


    public function index(CreateUserRequest $request, CustomerService $customerService)
    {   
        try {
            //Authenticate
            $data=$customerService->getCustomerData($request, 1);

            //Send http status out
            return $this->response->success(compact('data'));
        } catch(Exception $e) {

        } //Try-catch ends

    } //Function ends



    public function update(UpdateCustomerRequest $request, CustomerService $customerService)
    {   
        try {
            //Authenticate
            $Customer=$customerService->createCustomer($request, 1);

            //Send the JSON response
            return response()
                ->json([], config('omnichannel.settings.http_status_code.success'));
        } catch(Exception $e) {

        } //Try-catch ends

    } //Function ends


} //Class ends
