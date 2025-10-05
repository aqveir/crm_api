<?php

namespace Modules\Core\Http\Controllers\Backend;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;

use Modules\Core\Http\Requests\Backend\Lookup\FetchLookupRequest;
use Modules\Core\Http\Requests\Backend\Lookup\CreateLookupRequest;
use Modules\Core\Http\Requests\Backend\Lookup\UpdateLookupRequest;

use Modules\Core\Models\Lookup\Lookup;
use Modules\Core\Services\Lookup\LookupService;

use Modules\Core\Transformers\Response\LookupMinifiedResource;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Controller for Lookup Data
 */
class LookupController extends ApiBaseController
{

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(Lookup::class, 'lookup');
    }


    /**
     * Get All Lookup
     * 
     * @param \Modules\Core\Http\Requests\Backend\Lookup\FetchLookupRequest $request
     * @param \Modules\Core\Services\Lookup\LookupService $service
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/lookup",
     *     tags={"Lookup"},
     *     operationId="api.lookup.index",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=401, description="Authorization Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(FetchLookupRequest $request, LookupService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch all lookup data
            $result = $service->index($orgHash, $payload);

            //Transform data
            $data = new LookupMinifiedResource($result);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Get Lookup Data by Key
     * 
     * @param \Modules\Core\Http\Requests\Backend\Lookup\FetchLookupRequest $request
     * @param \Modules\Core\Services\Lookup\LookupService $service
     * @param \string $key
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/lookup/{key}",
     *     tags={"Lookup"},
     *     operationId="api.lookup.show",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(name="key", in="path", description="Key", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show(FetchLookupRequest $request, LookupService $service, string $subdomain, Lookup $lookup)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch all lookup data
            $data = $service->show($orgHash, $payload, $lookup['key']);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Create Lookup Data
     * 
     * @param \Modules\Core\Http\Requests\Backend\Lookup\CreateLookupRequest $request
     * @param \Modules\Core\Services\Lookup\LookupService $service
     * @param \string $key
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/lookup",
     *     tags={"Lookup"},
     *     operationId="api.lookup.create",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function create(CreateLookupRequest $request, LookupService $service, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch all lookup data
            $data = $service->show($orgHash, $payload, $key);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Update Lookup Data by Key
     * 
     * @param \Modules\Core\Http\Requests\Backend\Lookup\UpdateLookupRequest $request
     * @param \Modules\Core\Services\Lookup\LookupService $service
     * @param \string $key
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/lookup/{key}",
     *     tags={"Lookup"},
     *     operationId="api.lookup.update",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Parameter(name="key", in="path", description="Key", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(UpdateLookupRequest $request, LookupService $service, string $subdomain, Lookup $lookup)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch all lookup data
            $data = $service->show($orgHash, $payload, $key);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

    
    /**
     * Delete Lookup Data by Key
     * 
     * @param \Modules\Core\Http\Requests\Backend\Lookup\FetchLookupRequest $request
     * @param \Modules\Core\Services\Lookup\LookupService $service
     * @param \string $key
     * 
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/lookup/{key}",
     *     tags={"Lookup"},
     *     operationId="api.lookup.destroy",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/organization_key"),
     *     @OA\Parameter(name="key", in="path", description="Key", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy(FetchLookupRequest $request, LookupService $service, string $subdomain, Lookup $lookup)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Create payload
            $payload = collect($request);

            //Fetch all lookup data
            $data = $service->show($orgHash, $payload, $key);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends
    
} //Class ends