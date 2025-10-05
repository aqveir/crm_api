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
        'middleware' => ['remote_endpoint_auth'],
        'namespace' => 'Modules\MailParser\Http\Controllers',
        'domain' => config('aqveir.settings.domain')
    ], function (Router $api) {

    // Unauthenticated Endpoints for Mail Parser
    $api->group(['prefix' => 'v1'], function(Router $api) {

        // Exotel Endpoints
        $api->group(['prefix' => 'mailparser'], function(Router $api) {
            //zapier Calls
            $api->post('zapier','Zapier\MailParserController@create');
        });
    });
});