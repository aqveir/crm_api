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
        'namespace' => 'Modules\Core\Http\Controllers',
        'domain' => config('crmomni.settings.domain')
    ], function (Router $api) {

    // Unauthenticated OR Guest endpoints
    $api->group(['middleware' => ['guest']], function(Router $api) {
        // Organization Endpoint
        $api->group(['prefix' => 'organization'], function(Router $api) {
        });
    });

    // Authenticated Endpoints for Backend
    $api->group(['middleware' => ['auth:backend']], function(Router $api) {
        // Organization Endpoint
        $api->group(['prefix' => 'organization'], function(Router $api) {
            $api->post('/', 'Backend\\Organization\\OrganizationController@create');
            $api->get('{organization}', 'Backend\\Organization\\OrganizationController@update');

            $api->get('/', 'Backend\\Organization\\OrganizationController@index');
            $api->get('{organization}', 'Backend\\Organization\\OrganizationController@show');
        });

        // Lookup Endpoint
        $api->group(['prefix' => 'lookup'], function(Router $api) {
            $api->get('/', 'Backend\\Lookup\\LookupController@index');
            $api->get('{key}', 'Backend\\Lookup\\LookupController@show');

            $api->post('/', 'Backend\\Lookup\\LookupController@create');
            $api->put('{key}', 'Backend\\Lookup\\LookupController@update');
            $api->delete('{key}', 'Backend\\Lookup\\LookupController@destroy');
        });

        // Role Endpoint
        $api->group(['prefix' => 'role'], function(Router $api) {
            $api->get('/', 'Backend\\Role\\RoleController@index');
            $api->get('{key}', 'Backend\\Role\\RoleController@show');

            $api->post('/', 'Backend\\Role\\RoleController@create');
            $api->put('{key}', 'Backend\\Role\\RoleController@update');
            $api->delete('{key}', 'Backend\\Role\\RoleController@destroy');
        });
    });
});