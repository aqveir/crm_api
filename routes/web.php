<?php

use Illuminate\Support\Facades\Route;

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
Route::get('reset_password/{token}/crm', ['as' => 'crmomni.user.password.reset', function(string $token, Request $request) {
	$backendUri = config('crmomni.setting.backend_uri');
	return redirect($backendUri.'/reset/'.$token);
}]);

// Route for the web console
Route::get('/', function(Request $request) {
	$backendUri = config('crmomni.setting.backend_uri');
	return redirect($backendUri);
});
