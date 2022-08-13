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
        'namespace' => 'Modules\Account\Http\Controllers',
        'domain' => config('aqveir.settings.domain')
    ], function (Router $api) {

    // Authenticated Endpoints for Backend
    $api->group(['middleware' => ['auth:backend']], function(Router $api) {

        // Note Endpoints
        $api->group(['prefix' => 'account'], function(Router $api) {
            $api->get('{accounts}', 'Backend\\AccountController@show');
            $api->get('/', 'Backend\\AccountController@index');
            
            $api->post('/', 'Backend\\AccountController@create');
            $api->put('{accounts}', 'Backend\\AccountController@update');
            $api->delete('{accounts}', 'Backend\\AccountController@destroy');
        });
    });
});