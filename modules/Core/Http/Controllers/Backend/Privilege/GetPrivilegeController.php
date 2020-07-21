<?php

namespace Modules\Core\Http\Controllers\Backend\Privilege;

use Auth;
use Config;

use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Controller to Get the Privileges Data
 */
class GetPrivilegeController extends ApiBaseController
{
    /**
     * Create a new GetOrganizationController instance.
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

        $data = $this->getResponse();
        if($data==null) {
            throw new BadRequestHttpException();
        } //End if

        return response()->json([
            'privileges' => $data
        ], config('portiqo-crm.http_status_code.success'));
    } //Method ends
    

    public function getData(JWTAuth $JWTAuth, $id)
    {
        // Get user object from the token
        $authenticatedUser = Auth::guard()->user();
        if(!$authenticatedUser) {
            throw new AccessDeniedHttpException();
        } //End if

        $data = $this->getResponse($id);
        if($data==null) {
            throw new BadRequestHttpException();
        } //End if

        return response()->json([
            'privilege' => $data
        ], config('portiqo-crm.http_status_code.success'));
    } //Method ends

    /**
     * Function to query the DB and return object
     *
     * @return objReturnValue
     */
    private function getResponse(int $id=0)
    {
        $objReturnValue = null;

        if ($id>0) 
        {
            $query = config('portiqo-crm.class_model.privilege')::where('id', $id);
            $objReturnValue = $query->orderBy('id', 'asc')->firstOrFail();
        }
        else
        {
            $objReturnValue = config('portiqo-crm.class_model.privilege')::all();
        } //End if-else
        
        return $objReturnValue;
    } //Method ends
} //Class ends
