<?php

namespace Modules\Core\Http\Controllers\Backend;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Modules\Core\Models\Organization\Organization;

use Modules\Core\Http\Controllers\ApiBaseController;

use Modules\Core\Http\Requests\Backend\Organization\CreateOrganizationRequest;
use Modules\Core\Http\Requests\Backend\Organization\UpdateOrganizationRequest;
use Modules\Core\Http\Requests\Backend\Organization\DeleteOrganizationRequest;

use Modules\Core\Services\Organization\OrganizationService;

use Modules\Core\Transformers\Response\Organization\OrganizationResource;
use Modules\Core\Transformers\Response\Organization\OrganizationMiniResource;

use Symfony\Component\HttpFoundation\Response;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Controller for Organization Data
 */
class OrganizationController extends ApiBaseController
{

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(Organization::class, 'organization');
    }


    /**
     * Get All Organizations
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Core\Services\Organization\OrganizationService $organizationService
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/organization",
     *     tags={"Organization"},
     *     operationId="api.organization.get.all",
     *     security={{"omni_token":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=401, description="Authorization Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(Request $request, OrganizationService $organizationService, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Fetch all organizations data
            $result = $organizationService->getAll($payload, true);

            //Transform data
            $data = new OrganizationMiniResource($result);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Get Organization Data by Identifier
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Core\Services\Organization\OrganizationService $organizationService
     * @param \string $subdomain
     * @param \Organization $organization
     * 
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/organization/{hash}",
     *     tags={"Organization"},
     *     operationId="api.organization.get.show",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(ref="#/components/parameters/hash_identifier"),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show(Request $request, OrganizationService $organizationService, string $subdomain, Organization $organization)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Fetch organization data
            $result = $organizationService->getData($payload, $organization['hash'], true);

            //Transform data
            $data = new OrganizationResource($result);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends


    /**
     * Create Organization
     * 
     * @param \Modules\Core\Http\Requests\Backend\Organization\CreateOrganizationRequest $request
     * @param \Modules\Core\Services\Organization\OrganizationService $organizationService
     * @param \string $subdomain
     *
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Post(
     *     path="/organization",
     *     tags={"Organization"},
     *     operationId="api.organization.create",
     *     description="Creates a new organization. Duplicates are not allowed.",
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     * 
     */
    public function create(CreateOrganizationRequest $request, OrganizationService $organizationService, string $subdomain)
    {
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Fetch all organizations data
            $data = $organizationService->create($payload, $ipAddress);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        } //Try-catch ends
    } //Function ends


    /**
     * Update Organization
     * 
     * @param \Modules\Core\Http\Requests\Backend\Organization\UpdateOrganizationRequest $request
     * @param \Modules\Core\Services\Organization\OrganizationService $organizationService
     * @param \string $subdomain
     * @param \Modules\Core\Models\Organization\Organization $organization
     * 
     *
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Put(
     *     path="/organization/{hash}",
     *     tags={"Organization"},
     *     operationId="api.organization.update",
     *     description="Creates a new organization. Duplicates are not allowed.",
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     * 
     */
    public function update(\Illuminate\Http\Request $request, OrganizationService $organizationService, string $subdomain, Organization $organization)
    {
        $objReturnValue=null;
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            $file=null;
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
            } //End if

            //Update organizations data
            $data = $organizationService->update($orgHash, $payload, $file, $ipAddress);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        } //Try-catch ends
    } //Function ends


    /**
     * Delete Organization
     * 
     * @param \Modules\Core\Http\Requests\Backend\Organization\CreateOrganizationRequest $request
     * @param \Modules\Core\Services\Organization\OrganizationService $organizationService
     * @param \string $subdomain
     * @param \Modules\Core\Models\Organization\Organization $organization
     *
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Delete(
     *     path="/organization/{hash}",
     *     tags={"Organization"},
     *     operationId="api.organization.delete",
     *     description="Creates a new organization. Duplicates are not allowed.",
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     * 
     */
    public function destroy(DeleteOrganizationRequest $request, OrganizationService $organizationService, string $subdomain, Organization $organization)
    {
        $objReturnValue=null;
        try {
            //Get Org Hash 
            $orgHash = $this->getOrgHashInRequest($request, $subdomain);

            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Update organizations data
            $data = $organizationService->delete($orgHash, $payload, $ipAddress);

            //Send response data
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        } //Try-catch ends
    } //Function ends

} //Class ends