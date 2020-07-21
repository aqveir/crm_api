<?php

namespace Modules\Core\Http\Controllers\Backend\Organization;

use Auth;
use Config;

use App\Models\Role\Role;
use App\Models\User\User;
use App\Models\Organization\Organization;
use App\Models\Role\Traits\Action\RoleAction;
use App\Models\User\Traits\Action\UserAction;
use App\Models\Lookup\LookupValue;
use App\Models\Organization\Traits\Action\OrganizationAction;

use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use Modules\Core\Http\Requests\Organization\CreateOrganizationRequest;
use Modules\Core\Http\Requests\Organization\UpdateOrganizationRequest;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Controller to Create the Organization
 */
class SaveOrganizationController extends ApiBaseController
{
    use UserAction;
    use OrganizationAction;

    /**
     * Create SaveOrganizationController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', []);
    }

    /**
     * @OA\Post(
     *     path="/organization",
     *     tags={"Organization"},
     *     operationId="api.organization.create",
     *     description="Creates a new organization. Duplicates are not allowed.",
     *     security={{"JWT_Bearer_Auth":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     * 
     * Create New Organization
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateOrganizationRequest $request, JWTAuth $JWTAuth)
    {
        // Get user object from the token
        $authenticatedUser = Auth::guard()->user();
        if(!$authenticatedUser) {
            throw new HttpException(500);
        } //End if

        //Create New Organization with default values of Roles, Permissions, Users
        try {
            //Create Fresh Organization
            $orgId = $this->createNewOrganization($authenticatedUser, $request);
            if(!$orgId>0) { throw new HttpException(500); } //End if

            //Create New User
            $userNew = $this->createNewUser($authenticatedUser, $request, $orgId);
            if(!$userNew && $userNew->id>0) { throw new HttpException(500); } //End if

            //Create New Role For User
            $roleId = $this->createNewRole($authenticatedUser, $request, $orgId);
            if(!$roleId>0) { throw new HttpException(500); } //End if

            if($orgId>0) {
                //Save Lookup Data For That Organization
                $lookupData = $this->saveLookupData($orgId);
                //log::debug('default lookup data ->' .json_encode($lookupData, JSON_PRETTY_PRINT));
            } //End if

            //Assign Role to the New User
            $userNew->roles()->attach($roleId, 
                ['description' => config('portiqo-crm.settings.new_organization.default_text')]);

        } catch (Exception $e) {
            throw new HttpException(500);
        } //Try-catch ends

        return response()->json([
        ], config('portiqo-crm.http_status_code.success'));
    } //Function ends


    /**
     * Create New Organization based on the Request Params
     *
     * @return integer
     */
    private function createNewOrganization($authenticatedUser, $request) {
        $intReturnValue=0;
        try {
            //Get request parameters and save
            $organization = new Organization($request->all());
            $organization->created_by=$authenticatedUser->id;
            $organization->modified_by=0;
            if(!$organization->save()) {
                throw new HttpException(500);
            } //End if

            //Get Id of the Newly Created Organization
            $intReturnValue = $organization->id;

        } catch (Exception $e) {
            $intReturnValue=0;
        } //Try-catch ends
        return $intReturnValue;
    } //Function ends


    /**
     * Create New Default Role for the New Organization
     *
     * @return integer
     */
    private function createNewRole($authenticatedUser, $request, $orgId) {
        $intReturnValue=0;
        try {
            //Create New Elevated Role for this Organization
            $role = new Role(config('portiqo-crm.settings.new_organization.default_role'));
            $role->org_id=$orgId;
            if(!$role->save()) {
                throw new HttpException(500);
            } //End if

            //Get Id of the Newly Created Role
            $intReturnValue = $role->id;

            //Assign Privileges for the New Role
            $role->privileges()->attach(config('portiqo-crm.settings.new_organization.default_permissions'));
        } catch (Exception $e) {
            $intReturnValue=0;
        } //Try-catch ends

        return $intReturnValue;
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
