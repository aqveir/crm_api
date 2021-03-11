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
        'namespace' => 'Modules\Subscription\Http\Controllers',
        'domain' => config('crmomni.settings.domain')
    ], function (Router $api) {

    // Authenticated Endpoints for Backend
    $api->group(['middleware' => ['guest']], function(Router $api) {

        // Subscription Endpoints
        $api->group(['prefix' => 'subscription'], function(Router $api) {
            $api->get('active', 'Frontend\\SubscriptionController@index');
        });
    });

    // Authenticated Endpoints for Backend
    $api->group(['middleware' => ['auth:backend']], function(Router $api) {

        // Subscription Endpoints
        $api->group(['prefix' => 'subscription'], function(Router $api) {
            $api->get('/', 'Backend\\SubscriptionController@index');
            
            $api->post('/', 'Backend\\SubscriptionController@create');
            $api->put('{subscription}', 'Backend\\SubscriptionController@update');
            $api->delete('{subscription}', 'Backend\\SubscriptionController@destroy');
        });
    });
});