<?php

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

Route::prefix('user')->group(function() {
    Route::get('/', 'UserController@index');

    //Verify New User in Existing Organization
    Route::get('verify/{token}', 'Backend\\User\\SetUserController@verify');

    //Activate New User and Create New Organization
    Route::get('activate/{token}', 'Backend\\User\\SetUserController@activate');
});

//Reset Password Link For CRM Users
Route::get('reset/{token}', ['as' => 'password.reset', function($token, Request $req) {
	//$baseuri_webapp = config('crmomni.settings.base_url.web_app');
    //return redirect($baseuri_webapp.'web/reset/'.$token);
    //return redirect('web/reset/'.$token);
    dd($token);
}]);
