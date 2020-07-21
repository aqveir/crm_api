<?php

namespace Modules\Core\Http\Controllers\Backend\Role;

use Auth;
use Config;
use Tymon\JWTAuth\JWTAuth;

use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Controller to Get Roles Data
 */
class GetRoleController extends ApiBaseController
{
    /**
     * Create a new GetRoleController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', []);
    }


    public function getAll(JWTAuth $JWTAuth)
    {
        // Get user object from the token
        $authenticatedUser = Auth::guard()->user();
        if(!$authenticatedUser) {
            throw new AccessDeniedHttpException();
        } //End if

        // Check for Organization Id
        $orgId=$authenticatedUser->org_id;
        if($orgId<1) {
            throw new AccessDeniedHttpException();
        } //End if

        $data = $this->getResponse($orgId);
        if($data==null) {
            throw new BadRequestHttpException();
        } //End if

        return response()->json([
            'roles' => $data
        ], config('portiqo-crm.http_status_code.success'));
    } //Function ends
    

    public function getData(JWTAuth $JWTAuth, $id)
    {
        // Get user object from the token
        $authenticatedUser = Auth::guard()->user();
        if(!$authenticatedUser) {
            throw new AccessDeniedHttpException();
        } //End if

        $data = $this->getResponse(0, $id);
        if($data==null) {
            throw new BadRequestHttpException();
        } //End if

        return response()->json([
            'role' => $data
        ], config('portiqo-crm.http_status_code.success'));
    } //Function ends

    /**
     * Function to query the DB and return object
     *
     * @return objReturnValue
     */
    private function getResponse(int $orgId=0, int $id=0)
    {
        $objReturnValue = null;

        //Generte query
        if ($orgId>0) 
        {
            $query = config('portiqo-crm.class_model.role')::where('org_id', $orgId);
            $query = $query->orderBy('id', 'asc')->get();

            foreach($query as $role) {
                $role->privileges;
            }
            $objReturnValue = $query;
        } //End if
        if ($id>0) 
        {
            $query = config('portiqo-crm.class_model.role')::where('id', $id);
            $query = $query->orderBy('id', 'asc')->firstOrFail();

            $query->privileges;
            $objReturnValue = $query;
        } //End if

        return $objReturnValue;
    } //Function ends
} //Class ends
