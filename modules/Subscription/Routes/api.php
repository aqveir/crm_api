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
        'domain' => config('aqveir.settings.domain')
    ], function (Router $api) {

    // Authenticated Endpoints for Backend
    $api->group(['middleware' => ['guest']], function(Router $api) {

        // Subscription Endpoints
        $api->group(['prefix' => 'subscription'], function(Router $api) {
            $api->get('plan', 'Frontend\\PlanController@index');
        });
    });

    // Authenticated Endpoints for Backend
    $api->group(['middleware' => ['auth:backend']], function(Router $api) {

        // Organization Endpoints
        $api->group(['prefix' => 'organization/paymentmethod'], function(Router $api) {
            $api->get('fetch', 'Backend\\PaymentMethodController@index');
            $api->get('intent', 'Backend\\PaymentMethodController@intent');
            
            $api->post('/', 'Backend\\PaymentMethodController@create');
            $api->put('{uuid}', 'Backend\\PaymentMethodController@update');
            $api->delete('{uuid}', 'Backend\\PaymentMethodController@destroy');
        });

        // Subscription Endpoints
        $api->group(['prefix' => 'subscription'], function(Router $api) {

            //Subscription-Plans (Pricing) Endpoints
            $api->group(['prefix' => 'plan'], function(Router $api) {
                $api->post('/', 'Backend\\PlanController@create');
                $api->put('/', 'Backend\\PlanController@update');
            });

            $api->get('/', 'Backend\\SubscriptionController@index');
            $api->get('{uuid}', 'Backend\\SubscriptionController@show');
            
            $api->post('/', 'Backend\\SubscriptionController@create');
            $api->put('{subscription}', 'Backend\\SubscriptionController@update');
            $api->delete('{subscription}', 'Backend\\SubscriptionController@destroy');
        });
    });
});