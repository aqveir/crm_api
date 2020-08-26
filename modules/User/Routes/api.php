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

        // User Endpoint
        $api->group(['prefix' => 'user'], function(Router $api) {
            // Authentication
            $api->post('login', 'Modules\\User\\Http\\Controllers\\Backend\\Auth\\UserAuthController@login');

            // Password Management
            $api->post('forgot', 'Modules\\User\\Http\\Controllers\\Backend\\Auth\\UserAuthController@forgot');
            $api->post('reset', 'Modules\\User\\Http\\Controllers\\Backend\\Auth\\UserAuthController@reset');

            // User Exists Validation
            $api->get('exists', 'Modules\\User\\Http\\Controllers\\Backend\\User\\GetUserController@exists');

            // User Activation
            $api->get('activate/{token}', 'Modules\\User\\Http\\Controllers\\Backend\\User\\SetUserController@activate');

            // User Registration
            $api->post('register', 'Modules\\User\\Http\\Controllers\\Backend\\User\\SetUserController@register');

            // User Availability Status
            $api->get('status/{key}', 'Modules\\User\\Http\\Controllers\\Backend\\User\\GetUserController@detail');
        });
    });

    // Authenticated Endpoints for Backend
    $api->group(['middleware' => ['auth:backend']], function(Router $api) {

        // User Endpoints
        $api->group(['prefix' => 'user'], function(Router $api) {

            // User Availability Status
            $api->group(['prefix' => 'status'], function(Router $api) {
                $api->get('/', 'Modules\\User\\Http\\Controllers\\Backend\\User\\GetUserAvailabilityController@view');
                $api->post('{key}', 'Modules\\User\\Http\\Controllers\\Backend\\User\\SetUserAvailabilityController@update');
            });
            $api->get('{hash}/status', 'Modules\\User\\Http\\Controllers\\Backend\\User\\GetUserAvailabilityController@show');

            // Logout
            $api->put('logout', 'Modules\\User\\Http\\Controllers\\Backend\\Auth\\UserAuthController@logout');

            // Password Management
            $api->put('changepass', 'Modules\\User\\Http\\Controllers\\Backend\\Auth\\UserAuthController@changePassword');

            // User Management
            $api->get('/', 'Modules\\User\\Http\\Controllers\\Backend\\User\\GetUserController@index');
            $api->get('{hash}', 'Modules\\User\\Http\\Controllers\\Backend\\User\\GetUserController@show');
            $api->post('/', 'Modules\\User\\Http\\Controllers\\Backend\\User\\SetUserController@create');
            $api->put('{hash}', 'Modules\\User\\Http\\Controllers\\Backend\\User\\SetUserController@update');
            $api->put('{hash}/roles', 'Modules\\User\\Http\\Controllers\\Backend\\User\\SetUserController@assignRoles');
        });
    });
});