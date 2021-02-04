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
        'namespace' => 'Modules\User\Http\Controllers',
        'domain' => config('crmomni.settings.domain')
    ], function (Router $api) {

    // Unauthenticated OR Guest endpoints
    $api->group(['middleware' => ['guest']], function(Router $api) {

        // User Endpoint
        $api->group(['prefix' => 'user'], function(Router $api) {
            // Authentication
            $api->post('login', 'Backend\\Auth\\UserAuthController@login');

            // Password Management
            $api->post('forgot', 'Backend\\Auth\\UserAuthController@forgot');
            $api->post('reset', 'Backend\\Auth\\UserAuthController@reset');

            // User Exists Validation
            $api->get('exists', 'Backend\\User\\GetUserController@exists');

            // User Activation
            $api->get('activate/{token}', 'Backend\\User\\SetUserController@activate');

            // User Registration
            $api->post('register', 'Backend\\User\\SetUserController@register');

            // User Availability Status
            $api->get('status/{key}', 'Backend\\User\\GetUserController@detail');
        });
    });

    // Authenticated Endpoints for Backend
    $api->group(['middleware' => ['auth:backend']], function(Router $api) {

        // User Endpoints
        $api->group(['prefix' => 'user'], function(Router $api) {

            // User Availability Status
            $api->group(['prefix' => 'status'], function(Router $api) {
                $api->get('/', 'Backend\\User\\UserAvailabilityController@view');
                $api->post('{key}', 'Backend\\User\\UserAvailabilityController@update');
            });
            $api->get('{hash}/status', 'Backend\\User\\UserAvailabilityController@show');

            // Logout
            $api->put('logout', 'Backend\\Auth\\UserAuthController@logout');

            // Password Management
            $api->put('changepass', 'Backend\\Auth\\UserAuthController@changePassword');

            // Get User Profile
            $api->get('profile', 'Backend\\User\\GetUserController@profile');
        });

        // Organization Endpoints
        $api->group(['prefix' => 'organization/{ohash}/user'], function(Router $api) {
            // User Management
            $api->get('/', 'Backend\\User\\GetUserController@index');
            $api->get('{hash}', 'Backend\\User\\GetUserController@show');
            $api->post('/', 'Backend\\User\\SetUserController@create');
            $api->put('{hash}', 'Backend\\User\\SetUserController@update');
            $api->put('{hash}/roles', 'Backend\\User\\SetUserController@assignRoles');
        });
    });
});