<?php

namespace Modules\Core\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Modules\Boilerplate\Routing\Helpers;
use Modules\Core\Services\JsonResponseService;

use Modules\Core\Models\Organization\Organization;

use Exception;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

abstract class ApiBaseController extends CoreController
{
    //use Helpers;
    use AuthorizesRequests;
    
    
    /**
     * @var \Modules\Core\Services\JsonResponseService
     */
    protected $response;


    /**
     * MainController constructor.
     */
    public function __construct()
    {
        $this->response = new JsonResponseService;
    }


    /**
     * Function to query For Get Replaced Values For Payload
     *
     * @return objReturnValue
     */
    public function filterKey_find_callback(&$value, $key, $findReplaceValues) {        
        foreach ($findReplaceValues as $findReplaceValue) {
            $findValue = $findReplaceValue[0];
            $replaceValue = $findReplaceValue[1];

            if(is_string($value)) {
                $value = str_replace($findValue, $replaceValue, $value);
            } //End if           
        } //End Loop       
    } //Function ends


    /**
     * Check the key param in request and return the OrgHash value.
     */
    public function getOrgHashInRequest(Request $request) {
        return $request->has('key')?$request['key']:$this->getOrgHashFromHost($request);
    } //Function ends


    /**
     * Check the URL Host name in request and return the OrgHash value.
     */
    public function getOrgHashFromHost(Request $request) {
        $urlHost = $request->getHost();

        $organization = Organization::where('sub_domain', $urlHost)->first();
        if ($organization) {
            return $organization['hash'];
        } else {
            throw new AccessDeniedHttpException();
        } //End if
    } //Function ends


    /**
     * Get the IP address from the Request
     */
    public function getIpAddressInRequest(Request $request) {
        return $request->ip();
    } //Function ends


    /**
     * Get Current Authenticated User
     */
    public function getCurrentUser(string $orgHash=null) {

        //Get user 
        $user = Auth::guard('backend')->user();

        //Validate current user with provided organization
        if (!empty($orgHash)) {
            if ($user && $user['organization'] && ($user['organization']['hash']==$orgHash)) {
                $returnValue = $user;
            } else {
                $returnValue = null;
            } //End if
        } else {
            $returnValue = $user;
        } //End if

        return $returnValue;
    } //Function ends
    
} //Class ends