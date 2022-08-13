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

$api->version('v1', [
        'prefix' => 'api',
        'middleware' => ['api'],
        'namespace' => 'Modules\Contact\Http\Controllers',
        'domain' => config('aqveir.settings.domain')
    ], function (Router $api) {

    // Unauthenticated OR Guest endpoints
    $api->group(['middleware' => ['guest']], function(Router $api) {

        // Contact Endpoint
        $api->group(['prefix' => 'contact'], function(Router $api) {
            // Validate Contact's username
            $api->get('exists', 'Frontend\\Contact\\ContactAuthController@exists');

            // Register Contact
            $api->post('register', 'Frontend\\Contact\\ContactAuthController@register');

            // Authentication
            $api->post('login', 'Frontend\\Contact\\ContactAuthController@login');

            //Social Login
            $api->get('login/{social}', 'Frontend\\Contact\\ContactSocialAuthController@redirectToProvider');
            $api->any('login/{social}/callback', 'Frontend\\Contact\\ContactSocialAuthController@handleProviderCallback');

            // Password Management
            $api->post('forgot', 'Frontend\\Contact\\ContactAuthController@forgotPassword');
            $api->post('reset', 'Frontend\\Contact\\ContactAuthController@resetPassword');
        });
    });

    // Authenticated Endpoints for Frontend
    $api->group(['middleware' => ['auth:frontend']], function(Router $api) {

        // Contact Endpoints
        $api->group(['prefix' => 'contact'], function(Router $api) {
            // Logout
            $api->put('logout', 'Frontend\\Contact\\ContactAuthController@logout');

            // Password Management
            //$api->put('changepass', 'Frontend\\Auth\\ChangePasswordController@changePassword');

            // Contact Management
            //$api->get('profile', 'Frontend\\Contact\\ContactController@show');
            //$api->put('/', 'Modules\\Contacts\\Http\\Controllers\\Frontend\\Contact\\ContactController@update');
        });
    });

    // Authenticated Endpoints for Backend
    $api->group(['middleware' => ['auth:backend']], function(Router $api) {

        // Contact Endpoints
        $api->group(['prefix' => 'contact'], function(Router $api) {
            // Contact Management
            $api->post('fetch', 'Backend\\Contact\\ContactAPIController@index');
            $api->post('upload', 'Backend\\Contact\\ContactAPIController@upload');
            $api->get('{contact}', 'Backend\\Contact\\ContactAPIController@show');
            $api->post('/', 'Backend\\Contact\\ContactAPIController@create');
            $api->put('{contact}', 'Backend\\Contact\\ContactAPIController@update');
            $api->put('{contact}/avatar', 'Backend\\Contact\\ContactAPIController@updateAvatar');
            $api->delete('{contact}', 'Backend\\Contact\\ContactAPIController@destroy');

            // Telephony
            $api->post('{hash}/call', 'Backend\\Contact\\TelephonyController@call');
            $api->post('{hash}/call/{proxy}', 'Backend\\Contact\\TelephonyController@callToProxy');
        });
    });
});