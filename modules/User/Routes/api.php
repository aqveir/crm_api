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
        'domain' => config('aqveir.settings.domain')
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
            $api->get('exists', 'Backend\\User\\UserController@exists');

            // User Availability Status
            $api->get('status/{key}', 'Backend\\User\\UserAvailabilityController@detail');
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
            $api->get('profile', 'Backend\\User\\UserController@profile');

            // User Management
            $api->get('/', 'Backend\\User\\UserController@index');
            $api->get('{user}', 'Backend\\User\\UserController@show');
            $api->post('/', 'Backend\\User\\UserController@create');
            $api->put('{user}', 'Backend\\User\\UserController@update');
            $api->delete('{user}', 'Backend\\User\\UserController@destroy');
        });
    });
});