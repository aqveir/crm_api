<?php

namespace Modules\Contact\Http\Controllers\Backend\Contact;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\Contact\Http\Requests\Backend\Contact\GetAllContactRequest;

use Modules\Contact\Services\Contact\ContactService;

use Exception;
use Modules\Core\Exceptions\DuplicateDataException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Controller to Manage Contact Data on Backend
 */
class GetContactController extends ApiBaseController
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
     * Get All Contacts (Backend)
     *
     * @param \Modules\Api\Http\Requests\Frontend\Contact\GetAllContactRequest $request
     * @param \Modules\Contact\Services\Contact\ContactService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/contact",
     *      tags={"Contact"},
     *      operationId="api.backend.contact.all",
     *      security={{"omni_token":{}}},
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(GetAllContactRequest $request, ContactService $service)
    {   
        try {
            //Get collection of all contacts
            $data=$service->getContactData($request, 1);

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
     * Get Contact By Identifier (Backend)
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Contact\Services\Contact\ContactService $service
     * @param \string $hash
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/contact/{hash}",
     *      tags={"Contact"},
     *      operationId="api.backend.contact.data",
     *      security={{"omni_token":{}}},
     *      @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function data(Request $request, ContactService $service, string $hash)
    {   
        try {
            //Get data of the Contact
            $data=$service->getFullData($request, $hash);

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
