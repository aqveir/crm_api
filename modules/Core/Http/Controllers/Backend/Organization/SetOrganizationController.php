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
        }




        // // Get user object from the token
        // $authenticatedUser = Auth::guard()->user();
        // if(!$authenticatedUser) {
        //     throw new HttpException(500);
        // } //End if

        // //Create New Organization with default values of Roles, Permissions, Users
        // try {
        //     //Create Fresh Organization
        //     $orgId = $this->createNewOrganization($authenticatedUser, $request);
        //     if(!$orgId>0) { throw new HttpException(500); } //End if

        //     //Create New User
        //     $userNew = $this->createNewUser($authenticatedUser, $request, $orgId);
        //     if(!$userNew && $userNew->id>0) { throw new HttpException(500); } //End if

        //     //Create New Role For User
        //     $roleId = $this->createNewRole($authenticatedUser, $request, $orgId);
        //     if(!$roleId>0) { throw new HttpException(500); } //End if

        //     if($orgId>0) {
        //         //Save Lookup Data For That Organization
        //         $lookupData = $this->saveLookupData($orgId);
        //         //log::debug('default lookup data ->' .json_encode($lookupData, JSON_PRETTY_PRINT));
        //     } //End if

        //     //Assign Role to the New User
        //     $userNew->roles()->attach($roleId, 
        //         ['description' => config('portiqo-crm.settings.new_organization.default_text')]);

        // } catch (Exception $e) {
        //     throw new HttpException(500);
        // } //Try-catch ends

        // return response()->json([
        // ], config('portiqo-crm.http_status_code.success'));
    } //Function ends


    /**
     * Create New User for the New Organization based on Request Data
     *
     * @return object
     */
    private function createNewUser($authenticatedUser, $request, $orgId) {
        $objReturnValue=null;
        try {
            $user = new User($request->all());
            $user->org_id = $orgId;
            $user->user_name = $request->email;
            $user->password = config('portiqo-crm.settings.new_organization.default_password');
            $user->created_by=$authenticatedUser->id;
            $user->modified_by=0;
            if(!$user->save()) {
                throw new HttpException(500);
            } //End if

            $objReturnValue = $user;
        } catch (Exception $e) {
            $objReturnValue=null;
        } //Try-catch ends
        
        return $objReturnValue;
    } //Function ends


    /**
     * Save Default Lookup Data
     *
     * @return object
     */
    private function saveLookupData(int $orgId)
    {
        $objReturnValue=null; $lookupData=[];
        try {
            $lookups = config('portiqo-crm_org_creation.settings.default.lookups');
            foreach ($lookups as &$lookup) {
                $lookup['org_id'] = $orgId;
                array_push($lookupData, $lookup);
            } //End Loop
           
            //Insert Lookup Data 
            $isInsertSuccess = config('portiqo-crm.class_model.lookup_value')::insert($lookupData);
            //log::Debug('updated ->' . json_encode($isInsertSuccess, JSON_PRETTY_PRINT));

            if($isInsertSuccess) {
                $objReturnValue=$isInsertSuccess;
            } //End if
        } catch (Exception $e) {
            Log::error(json_encode($e));
            $objReturnValue=null;
        } //Try-catch ends

        return $objReturnValue;
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
