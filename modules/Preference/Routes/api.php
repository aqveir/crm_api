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
        'namespace' => 'Modules\Preference\Http\Controllers',
        'domain' => config('aqveir.settings.domain')
    ], function (Router $api) {

    // Authenticated Endpoints for Backend
    $api->group(['middleware' => ['auth:backend']], function(Router $api) {
        // Preference Endpoint
        $api->group(['prefix' => 'preference'], function(Router $api) {
            $api->get('/', 'Backend\\PreferenceController@index');
            $api->get('refresh', 'Backend\\PreferenceController@refresh');
            $api->get('{preference}', 'Backend\\PreferenceController@show');

            $api->post('/', 'Backend\\PreferenceController@create');
            $api->put('{preference}', 'Backend\\PreferenceController@update');
            $api->delete('{preference}', 'Backend\\PreferenceController@destroy');
        });
    });
});