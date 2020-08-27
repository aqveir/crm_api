<?php

namespace Modules\Core\Http\Controllers\Backend\Privilege;

use Config;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\Privilege\CreatePrivilegeRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Support\Facades\Log;
use Auth;

use App\Models\Privilege\Privilege;


class SavePrivilegeController extends Controller
{
    /**
     * Create a new SavePrivilegeController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', []);
    }

    public function create(CreatePrivilegeRequest $request, JWTAuth $JWTAuth)
    {
        // Get user object from the token
        $authenticatedUser = Auth::guard()->user();
        if(!$authenticatedUser) {
            throw new HttpException(500);
        } //End if

        //Get request parameters and save
        $privilege = new Privilege($request->all());
        if(!$privilege->save()) {
            throw new HttpException(500);
        } //End if

        //Send http status out
        return response()->json([
        ], config('portiqo-crm.http_status_code.success'));
    }
}
