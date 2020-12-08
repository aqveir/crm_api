<?php

use Modules\Boilerplate\Routing\Router;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
$api = app(Router::class);

$api->version('v1', function (Router $api) {

    // Unauthenticated OR Guest endpoints
    $api->group(['middleware' => ['guest']], function(Router $api) {

        // Contact Endpoint
        $api->group(['prefix' => 'contact'], function(Router $api) {
            // Validate Contact's username
            $api->get('exists', 'Modules\\Contact\\Http\\Controllers\\Frontend\\Contact\\ContactAuthController@exists');

            // Register Contact
            $api->post('register', 'Modules\\Contact\\Http\\Controllers\\Frontend\\Contact\\ContactAuthController@register');

            // Authentication
            $api->post('login', 'Modules\\Contact\\Http\\Controllers\\Frontend\\Contact\\ContactAuthController@login');

            //Social Login
            $api->get('login/{social}', 'Modules\\Contact\\Http\\Controllers\\Frontend\\Contact\\ContactSocialAuthController@redirectToProvider');
            $api->any('login/{social}/callback', 'Modules\\Contact\\Http\\Controllers\\Frontend\\Contact\\ContactSocialAuthController@handleProviderCallback');

            // Password Management
            $api->post('forgot', 'Modules\\Contact\\Http\\Controllers\\Frontend\\Contact\\ContactAuthController@forgotPassword');
            $api->post('reset', 'Modules\\Contact\\Http\\Controllers\\Frontend\\Contact\\ContactAuthController@resetPassword');
        });
    });

    // Authenticated Endpoints for Frontend
    $api->group(['middleware' => ['auth:frontend']], function(Router $api) {

        // Contact Endpoints
        $api->group(['prefix' => 'contact'], function(Router $api) {
            // Logout
            $api->put('logout', 'Modules\\Contact\\Http\\Controllers\\Frontend\\Contact\\ContactAuthController@logout');

            // Password Management
            $api->put('changepass', 'Modules\\Contact\\Http\\Controllers\\Frontend\\Auth\\ChangePasswordController@changePassword');

            // Contact Management
            $api->get('profile', 'Modules\\Contact\\Http\\Controllers\\Frontend\\Contact\\ContactController@show');
            $api->put('/', 'Modules\\Contacts\\Http\\Controllers\\Frontend\\Contact\\ContactController@update');
        });
    });

    // Authenticated Endpoints for Backend
    $api->group(['middleware' => ['auth:backend']], function(Router $api) {

        // Contact Endpoints
        $api->group(['prefix' => 'contact'], function(Router $api) {
            // Contact Management
            $api->post('fetch', 'Modules\\Contact\\Http\\Controllers\\Backend\\Contact\\GetContactController@index');
            $api->get('{hash}', 'Modules\\Contact\\Http\\Controllers\\Backend\\Contact\\GetContactController@show');
            $api->post('/', 'Modules\\Contacts\\Http\\Controllers\\Frontend\\Contact\\ContactController@create');
            $api->put('{hash}', 'Modules\\Contacts\\Http\\Controllers\\Frontend\\Contact\\ContactController@update');

            //Telephony
            $api->post('{hash}/call', 'Modules\\Contact\\Http\\Controllers\\Backend\\Contact\\TelephonyController@call');
            $api->post('{hash}/call/{proxy}', 'Modules\\Contact\\Http\\Controllers\\Backend\\Contact\\TelephonyController@callToProxy');
        });
    });
});