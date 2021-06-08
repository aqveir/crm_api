<?php

namespace Modules\Contact\Http\Controllers\Backend\Contact;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\Contact\Http\Requests\Backend\Contact\FetchContactRequest;
use Modules\Contact\Http\Requests\Backend\Contact\CreateContactRequest;
use Modules\Contact\Http\Requests\Backend\Contact\UpdateContactRequest;
use Modules\Contact\Http\Requests\Backend\Contact\DeleteContactRequest;
use Modules\Contact\Http\Requests\Backend\Contact\uploadContactRequest;

use Modules\Contact\Models\Contact\Contact;
use Modules\Contact\Services\Contact\ContactService;
use Modules\Contact\Services\Contact\ContactFileService;

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
class ContactAPIController extends ApiBaseController
{
    
    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(Contact::class, 'contact');
    }


    /**
     * Get All Contacts (Backend)
     * 
     * @param  \Modules\Contact\Http\Requests\Backend\Contact\FetchContactRequest $request
     * @param  \Modules\Contact\Services\Contact\ContactService $service
     * @param  \string $subdomain
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/contact/fetch",
     *      tags={"Contact"},
     *      operationId="api.backend.contact.index",
     *      security={{"omni_token":{}}},
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(FetchContactRequest $request, ContactService $service, string $subdomain)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Get collection of all contacts
            $data=$service->index($orgHash, $payload);

            //Send response data
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
     *      operationId="api.backend.contact.show",
     *      security={{"omni_token":{}}},
     *      @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show(Request $request, ContactService $service, string $subdomain, Contact $contact)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Get data of the Contact
            $data=$service->show($orgHash, $payload, $contact['hash']);

            //Send response data
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
     * Create Contact (Backend)
     *
     * @param  \Modules\Contact\Http\Requests\Backend\Contact\CreateContactRequest $request
     * @param  \Modules\Contact\Services\Contact\ContactService $service
     * @param  \string $subdomain
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/contact",
     *      tags={"Contact"},
     *      operationId="api.backend.contact.create",
     *      security={{"omni_token":{}}},
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(CreateContactRequest $request, ContactService $service, string $subdomain)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Get data of the Contact
            $data=$service->create($orgHash, $payload, $ipAddress);

            //Send response data
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
     * Update Contact by Identifier (Backend)
     *
     * @param  \Modules\Contact\Http\Requests\Backend\Contact\UpdateContactRequest $request
     * @param  \Modules\Contact\Services\Contact\ContactService $service
     * @param  \string $subdomain
     * @param  \Modules\Contact\Models\Contact\Contact $contact
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *      path="/contact/{hash}",
     *      tags={"Contact"},
     *      operationId="api.backend.contact.update",
     *      security={{"omni_token":{}}},
     *      @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(UpdateContactRequest $request, ContactService $service, string $subdomain, Contact $contact)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Get data of the Contact
            $data=$service->update($orgHash, $payload, $contact['hash'], $ipAddress);

            //Send response data
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
     * Delete Contact by Identifier (Backend)
     *
     * @param  \Modules\Contact\Http\Requests\Backend\Contact\DeleteContactRequest $request
     * @param  \Modules\Contact\Services\Contact\ContactService $service
     * @param  \string $subdomain
     * @param  \Modules\Contact\Models\Contact\Contact $contact
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *      path="/contact/{hash}",
     *      tags={"Contact"},
     *      operationId="api.backend.contact.delete",
     *      security={{"omni_token":{}}},
     *      @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy(DeleteContactRequest $request, ContactService $service, string $subdomain, Contact $contact)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Get data of the Contact
            $data=$service->delete($orgHash, $payload, $contact['hash'], $ipAddress);

            //Send response data
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
     * Update Contact by Identifier (Backend)
     *
     * @param  \Modules\Contact\Http\Requests\Backend\Contact\UploadContactRequest $request
     * @param  \Modules\Contact\Services\Contact\ContactService $service
     * @param  \string $subdomain
     * @param  \Modules\Contact\Models\Contact\Contact $contact
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/contact/upload",
     *      tags={"Contact"},
     *      operationId="api.backend.contact.upload",
     *      security={{"omni_token":{}}},
     *      @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *      @OA\Response(response=200, description="Request was successfully executed."),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Model Validation Error"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function upload(UploadContactRequest $request, ContactFileService $service, string $subdomain)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Uploaded file
            $files=null;
            if ($request->hasFile('files')) {
                $files = $request->file('files');
            } else {
                throw new BadRequestHttpException();
            } //End if

            //Create payload
            $payload = collect($request);

            //Get data of the Contact
            $data=$service->upload($orgHash, $payload, $files, $ipAddress);

            //Send response data
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
