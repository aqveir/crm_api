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

    // Unauthenticated Endpoints for Telephony
    $api->group(['prefix' => 'telephony', 'middleware' => ['guest']], function(Router $api) {

        // Exotel Endpoints
        $api->group(['prefix' => 'exotel'], function(Router $api) {
            //Exotels Calls
            $api->any('call/callback','Modules\\CloudTelephony\\Http\\Controllers\\Exotel\\VoiceController@callback');
            $api->any('call/passthru','Modules\\CloudTelephony\\Http\\Controllers\\Exotel\\VoiceController@passthru');

            //Exotels SMS
            $api->any('sms/callback','Modules\\CloudTelephony\\Http\\Controllers\\Exotel\\SmsController@callback');

            //TODO: Delete this
            $api->any('call/customer','Modules\\CloudTelephony\\Http\\Controllers\\Exotel\\VoiceController@test');
        });
    });
});