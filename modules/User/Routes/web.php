<?php

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Reset Password Link For CRM Users
Route::get('reset/{token}', array('as' => 'password.reset', 
    function(string $token, Request $request): RedirectResponse {

        //Get Host
        $host = $request->getSchemeAndHttpHost();

        //Get query data
        $queryData = $request->query();

        //Build query string
        $queryString = '';
        if ($queryData && is_array($queryData) && count($queryData)>0) {
            $queryDataCount = count($queryData);

            $index=0;
            foreach ($queryData as $key => $value) {
                if ($index==0) {
                    $queryString .= '?';
                } //End if

                $queryString .= $key . '=' . $value;

                if ($index>=0 && $queryDataCount>1 && $index<($queryDataCount-1)) {
                    $queryString .= '&';
                } //End if

                $index++;
            } //Loop ends
        } //End if

        //Build URL and Redirect 
        return redirect($host.'/web/user/reset/'.$token.$queryString);
    } //Function ends
));
