<?php

use Illuminate\Http\Request;
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

// Reset Password Link For CRM Users
// Route::get('reset_password/{token}/crm', ['as' => 'crmomni.user.password.reset', function(string $token, Request $request) {
// 	$backendUri = config('crmomni.settings.backend_uri');
// 	return redirect($backendUri.'/reset/'.$token);
// }]);

// Route for the web console
Route::domain(config('crmomni.settings.domain'))->group(function() {
	Route::get('/pricing', function() { return view('templates.crmomni.pages.pricing'); })->name('crmomni.site.pricing');
	Route::get('/contact', function() { return view('templates.crmomni.pages.contact'); })->name('crmomni.site.contact');

	Route::get('/faq', function() { return view('templates.crmomni.pages.faq'); })->name('crmomni.site.faq');

	Route::get('/terms_conditions', function() { return view('templates.crmomni.pages.terms_conditions'); })->name('crmomni.site.tnc');
	Route::get('/privacy_policy', function() { return view('templates.crmomni.pages.privacy_policy'); })->name('crmomni.site.policy');
	Route::get('/legal', function() { return view('templates.crmomni.pages.legal'); })->name('crmomni.site.legal');

	Route::get('/register', function() { return view('templates.crmomni.pages.register'); })->name('crmomni.site.register');

	// Default route
	Route::get('/', function(Request $request, string $subdomain) {
		if (in_array($subdomain , config('crmomni.settings.website'))) {
			return view('templates.crmomni.pages.index');
		} else {
			$backendUri = config('crmomni.settings.backend_uri');
			return redirect($backendUri);
		} //End if
	})->name('crmomni.site.index');
});