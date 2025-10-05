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
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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
    public function getOrgHashInRequest(Request $request, ?string $subdomain, bool $isForcedHostCheck=false) {
        try {
            $returnValue = null;

            //Get current user (from token)
            $user = $this->getCurrentUser();

            //Data check
            if (empty($subdomain) && empty($user)) {
                throw new UnauthorizedHttpException('ERROR_UNAUTHORIZED_ACCESS');
            } //End if

            //Get Whitelisted subdomains
            $subdomainsWhitelisted = config('aqveir.settings.whitelisted_subdomains');

            //Check administrative domain
            if (in_array($subdomain, $subdomainsWhitelisted) && (!$isForcedHostCheck)){
                $returnValue = $request->has('key')?$request['key']:$this->getOrgHashFromHost($subdomain, $user);
            } else {
                $returnValue = $this->getOrgHashFromHost($subdomain, $user, $isForcedHostCheck);
            } //end if

            if (empty($returnValue)) {
                throw new Exception();
            } //End if

            return $returnValue;
        } catch(Exception $e) {
            throw $e;
        } //try-catch ends
    } //Function ends


    /**
     * Check the URL Host name in request and return the OrgHash value.
     */
    public function getOrgHashFromHost(string $subdomain, $user, bool $isForcedHostCheck=false) {
        try {
            $returnValue = null;
            $orgHashSubDomain=null;

            //SubDomain check
            if (!empty($subdomain) || $isForcedHostCheck) {
                $organization = Organization::where('subdomain', $subdomain)->firstOrFail();
                if ($organization) {
                    $orgHashSubDomain = $organization['hash'];
                } //End if
            } //End if
            
            //Check user and domain conditions
            if (empty($user)) {
                if (empty($orgHashSubDomain)) {
                    throw new AccessDeniedHttpException();
                } else {
                    $returnValue = $orgHashSubDomain;
                } //End if
            } else {
                if (empty($orgHashSubDomain)) {
                    $returnValue = $user->organization['hash'];
                } else {
                    if ($user->organization['hash'] == $orgHashSubDomain) {
                        $returnValue = $orgHashSubDomain; 
                    } else {
                        throw new UnauthorizedHttpException('ERROR_UNAUTHORIZED_ACCESS');
                    } //End if
                } //End if
            } //End if
        } catch(Exception $e) {
            throw new AccessDeniedHttpException();
        } //Try-catch ends

        return $returnValue;
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
    public function getCurrentUser(string $orgHash=null, string $guard='backend') {
        $returnValue = null;

        //Get user 
        $user = Auth::guard($guard)->user();

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