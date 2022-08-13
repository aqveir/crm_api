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
        'namespace' => 'Modules\Document\Http\Controllers',
        'domain' => config('aqveir.settings.domain')
    ], function (Router $api) {

    // Authenticated Endpoints for Backend
    $api->group(['middleware' => ['auth:backend']], function(Router $api) {

        // Document Endpoints
        $api->group(['prefix' => 'document'], function(Router $api) {
            $api->get('{document}', 'Backend\\DocumentController@show');
            
            $api->post('/', 'Backend\\DocumentController@create');
            $api->put('{document}', 'Backend\\DocumentController@update');
            $api->delete('{document}', 'Backend\\DocumentController@destroy');
        });
    });
});