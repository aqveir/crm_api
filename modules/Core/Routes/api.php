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
        // Organization Endpoint
        $api->group(['prefix' => 'organization'], function(Router $api) {
            // Create
            $api->post('/', 'Modules\\Core\\Http\\Controllers\\Backend\\Organization\\SetOrganizationController@create');

            $api->get('/', 'Modules\\Core\\Http\\Controllers\\Backend\\Organization\\GetOrganizationController@index');
            $api->get('{hash}', 'Modules\\Core\\Http\\Controllers\\Backend\\Organization\\GetOrganizationController@data');
        });
    });

    // Authenticated Endpoints for Backend
    $api->group(['middleware' => ['auth:backend']], function(Router $api) { 
        // Lookup Endpoint
        $api->group(['prefix' => 'lookup'], function(Router $api) {
            $api->get('/', 'Modules\\Core\\Http\\Controllers\\Backend\\Lookup\\LookupController@index');
            $api->get('{key}', 'Modules\\Core\\Http\\Controllers\\Backend\\Lookup\\LookupController@show');

            $api->post('/', 'Modules\\Core\\Http\\Controllers\\Backend\\Lookup\\LookupController@create');
            $api->put('{key}', 'Modules\\Core\\Http\\Controllers\\Backend\\Lookup\\LookupController@update');
            $api->delete('{key}', 'Modules\\Core\\Http\\Controllers\\Backend\\Lookup\\LookupController@destroy');
        });

        // Role Endpoint
        $api->group(['prefix' => 'role'], function(Router $api) {
            $api->get('/', 'Modules\\Core\\Http\\Controllers\\Backend\\Role\\RoleController@index');
            $api->get('{key}', 'Modules\\Core\\Http\\Controllers\\Backend\\Role\\RoleController@show');

            $api->post('/', 'Modules\\Core\\Http\\Controllers\\Backend\\Role\\RoleController@create');
            $api->put('{key}', 'Modules\\Core\\Http\\Controllers\\Backend\\Role\\RoleController@update');
            $api->delete('{key}', 'Modules\\Core\\Http\\Controllers\\Backend\\Role\\RoleController@destroy');
        });
    });
});