<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Reset Password Link For CRM Users
Route::get('reset/{token}', ['as' => 'aqveir.user.password.reset', function(string $token, Request $request) {
	$backendUri = config('aqveir.settings.backend_uri');
	return redirect($backendUri.'/reset/'.$token);
}]);

// Route for the web console
Route::get('/', function(Request $request) {
	$backendUri = config('aqveir.settings.backend_uri');
	return redirect($backendUri);
});
