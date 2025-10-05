<?php

namespace Modules\Account\Http\Controllers\Backend;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;

use Modules\Account\Http\Requests\Backend\GetAccountRequest;
use Modules\Account\Http\Requests\Backend\CreateAccountRequest;
use Modules\Account\Http\Requests\Backend\UpdateAccountRequest;
use Modules\Account\Http\Requests\Backend\DeleteAccountRequest;

use Modules\Account\Transformers\Responses\AccountResource;
use Modules\Account\Transformers\Responses\AccountMinifiedResource;

use Modules\Account\Models\Account;
use Modules\Account\Services\AccountService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AccountController extends ApiBaseController
{
    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();
        //$this->authorizeResource(Account::class, 'account');
    }


    /**
     * Get All Accounts for an Organization
     *
     * @param \Modules\Account\Http\Requests\Backend\GetAccountRequest $request
     * @param \Modules\User\Services\AccountService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/account",
     *     tags={"Account"},
     *     operationId="api.backend.account.index",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(GetAccountRequest $request, AccountService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch Users data for Organization
            $response = $service->getAll($orgHash, $payload);

            //Transform data
            $data = new AccountMinifiedResource(collect($response));

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Show Account By Identifier
     *
     * @param \Modules\Account\Http\Requests\Backend\GetAccountRequest $request
     * @param \Modules\User\Services\AccountService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/account/{id}",
     *     tags={"Account"},
     *     operationId="api.backend.account.show",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show(GetAccountRequest $request, AccountService $service, string $subdomain, Account $accounts)
    {   
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch Account record
            $result = $service->show($orgHash, $payload, $accounts['id']);

            //Transform data
            $data = new AccountResource($result);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Create Account
     *
     * @param \Modules\Account\Http\Requests\Backend\CreateAccountRequest $request
     * @param \Modules\Account\Services\AccountService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/account",
     *     tags={"Account"},
     *     operationId="api.backend.account.create",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(CreateAccountRequest $request, AccountService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Create customer
            $result = $service->create($orgHash, $payload);

            //Transform data
            $data = new AccountResource($result);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(UnauthorizedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Update Account
     *
     * @param \Modules\Account\Http\Requests\Backend\UpdateAccountRequest $request
     * @param \Modules\Account\Services\AccountService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/account/{id}",
     *     tags={"Account"},
     *     operationId="api.backend.account.update",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(UpdateAccountRequest $request, AccountService $service, string $subdomain, Account $accounts)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Update account
            $result = $service->update($orgHash, $payload, $accounts['id']);

            //Transform data
            $data = new AccountResource($result);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Delete Account
     *
     * @param \Modules\Account\Http\Requests\Backend\DeleteAccountRequest $request
     * @param \Modules\Account\Services\AccountService $service
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/account/{id}",
     *     tags={"Account"},
     *     operationId="api.backend.account.delete",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy(DeleteAccountRequest $request, AccountService $service, string $subdomain, Account $accounts)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Delete account
            $data = $service->delete($orgHash, $payload, $accounts['id']);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([$e->getMessage()], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends
