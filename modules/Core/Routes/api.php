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
});