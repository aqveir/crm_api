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
    $api->group(['middleware' => ['guest']], function(Router $api) {

        // Exotel Endpoints
        $api->group(['prefix' => 'exotel'], function(Router $api) {
            //Exotels Call Details
            $api->any('call/callback','Modules\\CloudTelephony\\Http\\Controllers\\Exotel\\VoiceController@callback');
            $api->any('call/details','Modules\\CloudTelephony\\Http\\Controllers\\Exotel\\VoiceController@details');
            $api->any('call/passthru','Modules\\CloudTelephony\\Http\\Controllers\\Exotel\\VoiceController@passthru');

            //Exotels SMS Details
            $api->any('sms/details','Modules\\CloudTelephony\\Http\\Controllers\\Exotel\\SmsController@details');
        });
    });
});