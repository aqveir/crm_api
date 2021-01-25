<?php

namespace Modules\Core\Http\Controllers\Backend\Organization;

use Config;
use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;

use Modules\Core\Http\Requests\Backend\Organization\CreateOrganizationRequest;
use Modules\Core\Http\Requests\Backend\Organization\UpdateOrganizationRequest;

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
 * Controller to Create/Amend the Organization
 */
class SetOrganizationController extends ApiBaseController
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
     * Create Organizations
     * 
     * @param \Modules\Core\Http\Requests\Backend\Organization\CreateOrganizationRequest $request
     * @param \Modules\Core\Services\Organization\OrganizationService $organizationService
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
     * Create New Organization
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateOrganizationRequest $request, OrganizationService $organizationService)
    {
        try {
            //Get IP Address
            $ipAddress = $this->getIpAddressInRequest($request);

            //Create payload
            $payload = collect($request);

            //Fetch all organizations data
            $data = $organizationService->create($payload);

            //Send http status out
            return $this->response->success(compact('data'));
            
        } catch(AccessDeniedHttpException $e) {
            return $this->response->fail([], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return $this->response->fail([], Response::HTTP_BAD_REQUEST);
        } //Try-catch ends
    } //Function ends


    public function update(UpdateOrganizationRequest $request, JWTAuth $JWTAuth, $id)
    {
        $objReturnValue=null;
        try {
            // Get user object from the token
            $authenticatedUser = Auth::guard()->user();
            if(!$authenticatedUser) {
                throw new HttpException(500);
            } //End if

            if($id>0) {
                $isUpdate = $this->updateOrganization($id, $authenticatedUser->id, $request);
                //log::Debug('updated organiation ->' . json_encode($isUpdate));
                if(!$isUpdate) { throw new BadRequestHttpException(); } //End if

                return response()->json([
                    'status' => true,
                ], config('portiqo-crm.http_status_code.success'));
            } //End if
        } catch (Exception $e) {
            Log::error(json_encode($e));
            $objReturnValue=null;
        } //Try-catch ends

        return $objReturnValue;
    } //Function ends
    
} //Class ends
