<?php

namespace Modules\Core\Http\Controllers\Backend\Lookup;

use Config;
use Auth;

use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Illuminate\Support\Facades\Log;

use Modules\Core\Http\Controllers\ApiBaseController;
use App\Api\V1\Requests\Lookup\LookupRequest;
use App\Api\V1\Requests\Lookup\UpdateLookupRequest;
use App\Models\Lookup\Traits\Action\LookupValueAction;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Controller to Get Lookup Information
 */
class SaveLookupController extends ApiBaseController
{
    use LookupValueAction;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:api', []);
    }


    public function update(UpdateLookupRequest $request, int $id)
    {
        $objReturnValue=null;
        try {
            // Get user object from the token
            $authenticatedUser = Auth::guard()->user();
            if(!$authenticatedUser) {
                throw new AccessDeniedHttpException();
            } //End if
            
            $orgId=$authenticatedUser->org_id;
            if($orgId<1) {
                throw new AccessDeniedHttpException();
            } //End if

            $isUpdated = $this->updateLookupData($orgId, $id, $request);
            log::debug('updated lookup data ->' . json_encode($isUpdated));

            return response()->json([
                'Status' => $isUpdated,
            ], config('portiqo-crm.http_status_code.success'));

        } catch (Exception $e) {
            $objReturnValue=null;
            log::error(json_encode($e));
        } //Try-catch ends
    }  //Function ends
} //Class ends
