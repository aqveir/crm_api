<?php

namespace Modules\Core\Http\Controllers\Backend\Organization;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;

use Modules\Core\Services\Organization\OrganizationService;

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
class GetOrganizationController extends ApiBaseController
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
    public function index(Request $request, OrganizationService $organizationService)
    {
        try {
            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Fetch all organizations data
            $data = $organizationService->getAll($payload, true);

            //Send http status out
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
     * @param \string $hash
     * @param \Modules\Core\Services\Organization\OrganizationService $organizationService
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/organization/{hash}",
     *     tags={"Organization"},
     *     operationId="api.organization.get.data",
     *     security={{"omni_token":{}}},
     *     @OA\Parameter(name="hash", in="path", description="Organization Identifier", required=true),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function data(Request $request, string $hash, OrganizationService $organizationService)
    {
        try {
            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Fetch organization data
            $data = $organizationService->getData($payload, $hash, true);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        }
    } //Function ends

} //Class ends