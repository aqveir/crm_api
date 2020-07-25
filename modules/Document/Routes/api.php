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

    // Authenticated Endpoints for Backend
    $api->group(['middleware' => ['auth:backend']], function(Router $api) {

        // Document Endpoints
        $api->group(['prefix' => 'document'], function(Router $api) {
            $api->get('/', 'Modules\\Document\\Http\\Controllers\\Backend\\DocumentController@index');
            
            $api->post('/', 'Modules\\Document\\Http\\Controllers\\Backend\\DocumentController@create');
            $api->put('{id}', 'Modules\\Document\\Http\\Controllers\\Backend\\DocumentController@update');
            $api->delete('{id}', 'Modules\\Document\\Http\\Controllers\\Backend\\DocumentController@destroy');
        });
    });
});