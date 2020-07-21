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
class GetLookupController extends ApiBaseController
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

    /**
     * Get All Lookup Data
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/lookup",
     *     tags={"Lookup"},
     *     operationId="api.lookup.getall",
     *     security={{"JWT_Bearer_Auth":{}}},
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
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
            'lookups' => $data
        ], config('portiqo-crm.http_status_code.success'));
    } //Function ends
    
    /**
     * Get Lookup Data by Name
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/lookup/{name}",
     *     tags={"Lookup"},
     *     operationId="api.lookup.getdata",
     *     security={{"JWT_Bearer_Auth":{}}},
     *     @OA\Parameter(name="name", in="path", description="Name", required=true),
     *     @OA\Response(response=200, description="Request was successfully executed."),
     *     @OA\Response(response=422, description="Model Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function getData(JWTAuth $JWTAuth, $name)
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

        $data = $this->getResponse($orgId, $name);
        if($data==null) {
            throw new BadRequestHttpException();
        } //End if

        return response()->json([
            'lookup' => $data
        ], config('portiqo-crm.http_status_code.success'));
    } //Function ends

    /**
     * Function to query the DB and return object
     *
     * @return objReturnValue
     */
    private function getResponse(int $orgId, string $name='')
    {
        $objReturnValue = null;

        //Generte query
        $query = config('portiqo-crm.class_model.lookup')::whereHas('values', function ($inner_query) use ($orgId) {
            $inner_query->where('org_id', '=', $orgId);
        });
        if(strlen($name)>0) {
            $query = $query->where('name', $name);
        } //End if
        $query = $query->orderBy('id', 'asc')->get();
        $query->makeVisible(['id']);   

        //Get values for lookup
        if(strlen($name)>0) {
            foreach($query as $lookup) {
                $lookup->values;
                $objReturnValue = $lookup;
            } //Loop ends
        } else {
            foreach($query as $lookup) {
                $lookup->values;
            } //Loop ends
            $objReturnValue = $query;
        } //End if-else
        
        return $objReturnValue;
    } //Function ends

} //Class ends
