<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Modules\Boilerplate\Routing\Helpers;
use Modules\Core\Services\JsonResponseService;

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
        return $request->has('key')?$request['key']:null;
    } //Function ends


    /**
     * Get the IP address from the Request
     */
    public function getIpAddressInRequest(Request $request) {
        return $request->ip();
    } //Function ends
    
} //Class ends