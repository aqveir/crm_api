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
        'domain' => config('aqveir.settings.domain')
    ], function (Router $api) {

    // Unauthenticated OR Guest endpoints
    $api->group(['middleware' => ['guest']], function(Router $api) {
        // Organization Endpoint
        $api->group(['prefix' => 'organization'], function(Router $api) {
        });
    });

    // Authenticated Endpoints for Backend
    $api->group(['middleware' => ['auth:backend']], function(Router $api) {

        // Organization Endpoints
        $api->group(['prefix' => 'meta'], function(Router $api) {
            $api->get('country', 'Backend\\CountryController@index');
            $api->get('timezone', 'Backend\\TimezoneController@index');
        });

        // Organization Endpoints
        $api->group(['prefix' => 'organization'], function(Router $api) {
            $api->post('/', 'Backend\\OrganizationController@create');
            $api->put('{organization}', 'Backend\\OrganizationController@update');
            $api->delete('{organization}', 'Backend\\OrganizationController@destroy');

            $api->get('/', 'Backend\\OrganizationController@index');
            $api->get('{organization}', 'Backend\\OrganizationController@show');
        });

        // Privilege Endpoints
        $api->group(['prefix' => 'privilege'], function(Router $api) {
            $api->get('/', 'Backend\\PrivilegeController@index');
            $api->get('{privilege}', 'Backend\\PrivilegeController@show');

            $api->post('/', 'Backend\\PrivilegeController@create');
            $api->put('{privilege}', 'Backend\\PrivilegeController@update');
            $api->delete('{privilege}', 'Backend\\PrivilegeController@destroy');
        });

        // Lookup Endpoint
        $api->group(['prefix' => 'lookup'], function(Router $api) {
            $api->get('/', 'Backend\\LookupController@index');
            $api->get('{lookup}', 'Backend\\LookupController@show');

            $api->post('/', 'Backend\\LookupController@create');
            $api->put('{lookup}', 'Backend\\LookupController@update');
            $api->delete('{lookup}', 'Backend\\LookupController@destroy');
        });

        // Role Endpoint
        $api->group(['prefix' => 'role'], function(Router $api) {
            $api->get('/', 'Backend\\RoleController@index');
            $api->get('{role}', 'Backend\\RoleController@show');

            $api->post('/', 'Backend\\RoleController@create');
            $api->put('{role}', 'Backend\\RoleController@update');
            $api->delete('{role}', 'Backend\\RoleController@destroy');
        });
    });
});