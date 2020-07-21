<?php

namespace Modules\Core\Http\Controllers\Backend\Role;

use Auth;
use Config;
use Tymon\JWTAuth\JWTAuth;

use Illuminate\Support\Facades\Log;

use App\Models\Role\Traits\Action\RoleAction;
use App\Models\Role\Traits\Action\RolePrivilegeAction;

use Modules\Core\Http\Controllers\ApiBaseController;
use App\Api\V1\Requests\Role\UpdateRoleRequest;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Controller to Create Roles Data
 */
class UpdateRoleController extends ApiBaseController
{
    use RoleAction;
    use RolePrivilegeAction;

    /**
     * Create a new UpdateRoleController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', []);
    }


    public function update(UpdateRoleRequest $request, JWTAuth $JWTAuth, int $id)
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

            if($id>0) {
                //Update Role
                $isUpdateRole = $this->updateRole($orgId, $id, $request);
                //log::debug('updated Role ->' . json_encode($isUpdateRole, JSON_PRETTY_PRINT));
                if(!$isUpdateRole) { throw new BadRequestHttpException();  } //End if
            } //End if

            //Send http status out
            return response()->json([
                'Status' => 'Ok',
            ], config('portiqo-crm.http_status_code.success'));
        }  catch (BadRequestHttpException $e) {
           throw New BadRequestHttpException();
        } //Try-catch ends
    } //Function ends


} //Class ends
