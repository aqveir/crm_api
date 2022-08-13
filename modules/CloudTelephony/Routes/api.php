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
        'namespace' => 'Modules\CloudTelephony\Http\Controllers',
        'domain' => config('aqveir.settings.domain')
    ], function (Router $api) {

    // Unauthenticated Endpoints for Telephony
    $api->group(['prefix' => 'telephony', 'middleware' => ['guest']], function(Router $api) {

        // Exotel Endpoints
        $api->group(['prefix' => 'exotel'], function(Router $api) {
            //Exotels Calls
            $api->any('callback','Exotel\\VoiceController@callback');
            $api->any('passthru','Exotel\\VoiceController@passthru');

            //Exotels SMS
            //$api->any('sms/callback','Exotel\\SmsController@callback');

            //TODO: Delete this
            $api->any('call/customer','Exotel\\VoiceController@test');
        });

        // Twilio Endpoints
        $api->group(['prefix' => 'twilio'], function(Router $api) {
            //Exotels Calls
            $api->any('callback','Twilio\\VoiceController@callback');
            $api->any('passthru','Twilio\\VoiceController@passthru');

            //Exotels SMS
            //$api->any('sms/callback','Twilio\\SmsController@callback');

            //TODO: Delete this
            $api->any('call/customer','Twilio\\VoiceController@test');
        });
    });
});