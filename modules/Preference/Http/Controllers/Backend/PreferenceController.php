<?php

namespace Modules\Preference\Http\Controllers\Backend;

use Config;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;

use Modules\Preference\Http\Requests\Backend\FetchPreferenceRequest;
use Modules\Preference\Http\Requests\Backend\CreatePreferenceRequest;
use Modules\Preference\Http\Requests\Backend\UpdatePreferenceRequest;
use Modules\Preference\Http\Requests\Backend\DeletePreferenceRequest;

use Modules\Preference\Models\Preference\Preference;
use Modules\Preference\Services\PreferenceService;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class PreferenceController extends ApiBaseController
{
    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(Preference::class, 'preference');
    }


    /**
     * Get All Organization Preferences
     * 
     * @param \Modules\Preference\Http\Requests\Backend\FetchPreferenceRequest $request
     * @param \Modules\Preference\Services\PreferenceService $service
     * @param \string $subdomain
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/preference",
     *     tags={"Preference"},
     *     operationId="api.backend.preference.get.all",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=401, description="Authorization Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(FetchPreferenceRequest $request, PreferenceService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Fetch all organizations data
            $data = $service->getAll($orgHash, $payload);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Get Organization Preference Data by Identifier
     * 
     * @param \Modules\Preference\Http\Requests\Backend\FetchPreferenceRequest $request
     * @param \Modules\Preference\Services\PreferenceService $service
     * @param \string $subdomain
     * @param \int $id
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/preference/{id}",
     *     tags={"Preference"},
     *     operationId="api.backend.preference.get.show",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show(FetchPreferenceRequest $request, PreferenceService $service, string $subdomain, Preference $preference)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Fetch organization data
            $data = $service->show($orgHash, $payload, $preference['id']);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Create Preference
     *
     * @param \Modules\Preference\Http\Requests\Backend\CreatePreferenceRequest $request
     * @param \Modules\Preference\Services\PreferenceService $service
     * @param \string $subdomain
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/preference",
     *     tags={"Preference"},
     *     operationId="api.backend.preference.create",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(CreatePreferenceRequest $request, PreferenceService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Create customer
            $data = $service->create($orgHash, $payload);

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
     * Update Preference
     *
     * @param \Modules\Preference\Http\Requests\Backend\UpdatePreferenceRequest $request
     * @param \Modules\Preference\Services\PreferenceService $service
     * @param \string $subdomain
     * @param \int $id
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/preference/{id}",
     *     tags={"Preference"},
     *     operationId="api.backend.preference.update",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(UpdatePreferenceRequest $request, PreferenceService $service, string $subdomain, Preference $preference)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Update preference
            $data = $service->update($orgHash, $payload, $preference['id']);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Delete Preference
     *
     * @param \Modules\Preference\Http\Requests\Backend\DeletePreferenceRequest $request
     * @param \Modules\Preference\Services\PreferenceService $service
     * @param \string $subdomain
     * @param \int $id
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/preference/{id}",
     *     tags={"Preference"},
     *     operationId="api.backend.preference.delete",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy(DeletePreferenceRequest $request, PreferenceService $service, string $subdomain, Preference $preference)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Delete preference
            $data = $service->delete($orgHash, $payload, $preference['id']);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Refresh Organization Preferences
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Preference\Services\PreferenceService $service
     * @param \string $subdomain
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/preference/refresh",
     *     tags={"Preference"},
     *     operationId="api.backend.preference.refresh",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function refresh(Request $request, PreferenceService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Create customer
            $data = $service->refresh($orgHash, $payload);

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
    
} //Class ends
