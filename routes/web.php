<?php

use Illuminate\Http\Request;
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

// Route for the web console
Route::domain(config('aqveir.settings.domain'))->group(function() {
	// Default route
	Route::get('/', function(Request $request, string $subdomain) {
		$subdomainValid = true;

		//Check if the subdomains is part of restricted list
		$subdomainsRestricted = config('aqveir.settings.restricted_subdomains');
		$subdomainValid = (in_array($subdomain, $subdomainsRestricted))?false:true;

		//Get the backend URI
		$backendUri = config('aqveir.settings.backend_uri');

		//Route to the valid subdomain 
		if ($subdomainValid) {
			return redirect($backendUri);
		} else {
			return redirect($backendUri . config('aqveir.settings.frontend_error_uri'));
		} //End if
	});
});

// // Reset Password Link For CRM Users
// Route::get('reset/{token}', ['as' => 'aqveir.user.password.reset', function(string $token, Request $request) {
// 	$backendUri = config('aqveir.settings.backend_uri');
// 	return redirect($backendUri.'/reset/'.$token);
// }]);
