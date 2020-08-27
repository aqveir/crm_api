<?php

namespace Modules\Core\Http\Controllers\Backend\Role;

use Auth;
use Config;
use Tymon\JWTAuth\JWTAuth;

use App\Models\Role\Traits\Action\RoleAction;
use App\Models\Role\Traits\Action\RolePrivilegeAction;

use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use App\Api\V1\Requests\Role\CreateRoleRequest;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Controller to Create Roles Data
 */
class CreateRoleController extends ApiBaseController
{
    use RoleAction;
    use RolePrivilegeAction;
    /**
     * Create a new CreateRoleController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', []);
    }

    /**
     * Create New Role
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function create(CreateRoleRequest $request, JWTAuth $JWTAuth)
    {   
        $objReturnValue=null;
        try {
            // Get user object from the token
            $authenticatedUser = Auth::guard()->user();
            if(!$authenticatedUser) {
                throw new HttpException(500);
            } //End if

            // Check for Organization Id
            $orgId=$authenticatedUser->org_id;
            if($orgId<1) {
                throw new AccessDeniedHttpException();
            } //End if

            //Save User Role
            $role = $this->saveRole($orgId, $request);
            log::debug('User Role ->' . json_encode($role));
            if(!$role) { throw new BadRequestHttpException(); } //End if

        } catch (Exception $e) {
            Log::error(json_encode($e));
            $objReturnValue=null;
        } //Try-catch ends

        //Send http status out
        return response()->json([
            'Status' => 'Ok',
        ], config('portiqo-crm.http_status_code.success'));

    } //Function ends


} //Class ends
