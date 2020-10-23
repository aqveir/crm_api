<?php

namespace Modules\Contact\Http\Controllers\Backend\Contact;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\Api\Http\Requests\Backend\Contact\CreateContactRequest;
use Modules\Api\Http\Requests\Backend\Contact\UpdateContactRequest;

use Modules\Contact\Services\Contact\ContactService;

use Exception;
use Modules\Core\Exceptions\DuplicateDataException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SaveContactController extends ApiBaseController
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


    public function index(CreateUserRequest $request, ContactService $service)
    {   
        try {
            //Authenticate
            $data=$service->getContactData($request, 1);

            //Send http status out
            return $this->response->success(compact('data'));
        } catch(Exception $e) {

        } //Try-catch ends

    } //Function ends



    public function update(UpdateContactRequest $request, ContactService $service)
    {   
        try {
            //Authenticate
            $Contact=$service->createContact($request, 1);

            //Send the JSON response
            return response()
                ->json([], config('omnichannel.settings.http_status_code.success'));
        } catch(Exception $e) {

        } //Try-catch ends

    } //Function ends


} //Class ends
