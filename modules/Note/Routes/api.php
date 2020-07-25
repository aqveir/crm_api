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

        // Note Endpoints
        $api->group(['prefix' => 'note'], function(Router $api) {
            $api->get('/', 'Modules\\Note\\Http\\Controllers\\Backend\\NoteController@index');
            
            $api->post('/', 'Modules\\Note\\Http\\Controllers\\Backend\\NoteController@create');
            $api->put('{id}', 'Modules\\Note\\Http\\Controllers\\Backend\\NoteController@update');
            $api->delete('{id}', 'Modules\\Note\\Http\\Controllers\\Backend\\NoteController@destroy');
        });
    });
});