<?php

namespace Modules\Core\Http\Controllers\Backend\Organization;

use Config;
use Exception;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;

use Illuminate\Http\Request;

use Modules\Core\Services\Organization\OrganizationService;

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
    }


    /**
     * Get All Organizations
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/organization",
     *     tags={"Organization"},
     *     operationId="api.organization.getall",
     *     security={{"JWT_Bearer_Auth":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(Request $request, OrganizationService $organizationService)
    {
        $data = $organizationService->getAll($request, true);

        //Send http status out
        return $this->response->success(compact('data'));
    } //Function ends


    /**
     * Get Organization Data by Identifier
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/organization/{hash}",
     *     tags={"Organization"},
     *     operationId="api.organization.getdata",
     
     *     security={{"JWT_Bearer_Auth":{}}},
     *     @OA\Parameter(name="hash", in="path", description="User Identifier", required=true),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function data(Request $request, string $hash, OrganizationService $organizationService)
    {
        $data = $organizationService->getData($request, $hash, true);

        //Send http status out
        return $this->response->success(compact('data'));
    } //Function ends


} //Class ends