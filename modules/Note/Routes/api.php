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
        'namespace' => 'Modules\Note\Http\Controllers',
        'domain' => config('aqveir.settings.domain')
    ], function (Router $api) {

    // Authenticated Endpoints for Backend
    $api->group(['middleware' => ['auth:backend']], function(Router $api) {

        // Note Endpoints
        $api->group(['prefix' => 'note'], function(Router $api) {
            $api->get('/', 'Backend\\NoteController@index');
            
            $api->post('/', 'Backend\\NoteController@create');
            $api->put('{note}', 'Backend\\NoteController@update');
            $api->delete('{note}', 'Backend\\NoteController@destroy');
        });
    });
});