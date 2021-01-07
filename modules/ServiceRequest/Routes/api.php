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

    // Authenticated Endpoints for Frontend
    $api->group(['middleware' => ['auth:frontend']], function(Router $api) {

        // ServiceRequest Endpoints
        $api->group(['prefix' => 'servicerequest'], function(Router $api) {
        });
    });

    // Authenticated Endpoints for Backend
    $api->group(['middleware' => ['auth:backend']], function(Router $api) {

        // ServiceRequest Endpoints
        $api->group(['prefix' => 'servicerequest'], function(Router $api) {
            // ServiceRequest Management
            $api->get('/', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestController@index');
            $api->get('{hash}', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestController@show');
            $api->post('/', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestController@create');
            $api->put('{hash}', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestController@update');

            //Telephony
            $api->post('{hash}/call', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestTelephonyController@call');
            $api->post('{hash}/sms', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestTelephonyController@sms');

            //Task Events
            $api->post('{hash}/task', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestTaskController@create');
            $api->put('{hash}/task/{id}', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestTaskController@update');
            $api->put('{hash}/task/{id}/complete', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestTaskController@complete');
            $api->get('{hash}/task', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestTaskController@index');
            $api->get('{hash}/task/{id}', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestTaskController@show');

            //Calendar Events
            $api->post('{hash}/event', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestEventController@create');
            $api->put('{hash}/event/{id}', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestEventController@update');
            $api->get('{hash}/event', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestEventController@index');
            $api->get('{hash}/event/{id}', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestEventController@show');
    
            //Communication Events
            $api->post('{hash}/communication', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestCommunicationController@create');
            $api->put('{hash}/communication/{id}', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestCommunicationController@update');
            $api->get('{hash}/communication', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestCommunicationController@index');
            $api->get('{hash}/communication/{id}', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestCommunicationController@show');
    
            //Preferences
            $api->post('{hash}/preferences', 'Modules\\ServiceRequest\\Http\\Controllers\\Backend\\ServiceRequestPreferencesController@save');
            
            
            // //Notes
            // $api->get('{hash}/note', 'App\\Api\\V1\\Controllers\\ServiceRequest\\GetServiceRequestNoteController@getAll');
            // $api->get('{hash}/note/{id}', 'App\\Api\\V1\\Controllers\\ServiceRequest\\GetServiceRequestNoteController@getData');




            // $api->get('/', 'App\\Api\\V1\\Controllers\\ServiceRequest\\GetServiceRequestController@getAll');
            // $api->get('{hash}', 'App\\Api\\V1\\Controllers\\ServiceRequest\\GetServiceRequestController@getData');
            // $api->post('/', 'App\\Api\\V1\\Controllers\\ServiceRequest\\SaveServiceRequestController@create');
            // $api->put('{hash}',  ['middleware' => 'cansaveservicerequest', 'uses' =>'App\\Api\\V1\\Controllers\\ServiceRequest\\SaveServiceRequestController@update']);
    
            //Task Events
            // $api->post('{hash}/task', 'App\\Api\\V1\\Controllers\\ServiceRequest\\ServiceRequestEventController@createTaskEvent');
            // $api->put('{hash}/task/{id}', 'App\\Api\\V1\\Controllers\\ServiceRequest\\ServiceRequestEventController@updateTaskEvent');
            // $api->put('{hash}/task/{id}/complete', 'App\\Api\\V1\\Controllers\\ServiceRequest\\ServiceRequestEventController@updateTaskEventComplete');
    
            //Calendar Events
            // $api->post('{hash}/event', 'App\\Api\\V1\\Controllers\\ServiceRequest\\ServiceRequestEventController@createCalendarEvent');
            // $api->put('{hash}/event/{id}', 'App\\Api\\V1\\Controllers\\ServiceRequest\\ServiceRequestEventController@updateCalendarEvent');
            // $api->get('{hash}/event', 'App\\Api\\V1\\Controllers\\ServiceRequest\\GetServiceRequestEventController@getAll');
            // $api->get('{hash}/event/{id}', 'App\\Api\\V1\\Controllers\\ServiceRequest\\GetServiceRequestEventController@getData');
    
            //Communication Events
            // $api->get('{hash}/communication', 'App\\Api\\V1\\Controllers\\ServiceRequest\\GetServiceRequestCommunicationController@getAll');
            // $api->get('{hash}/communication/{id}', 'App\\Api\\V1\\Controllers\\ServiceRequest\\GetServiceRequestCommunicationController@getData');
    
            // //Notes
            // $api->get('{hash}/note', 'App\\Api\\V1\\Controllers\\ServiceRequest\\GetServiceRequestNoteController@getAll');
            // $api->get('{hash}/note/{id}', 'App\\Api\\V1\\Controllers\\ServiceRequest\\GetServiceRequestNoteController@getData');
    
            //Recommendation
            $api->post('{hash}/recommendation', 'App\\Api\\V1\\Controllers\\ServiceRequest\\SaveServiceRequestRecommendationController@create');
            $api->put('{hash}/recommendation/{id}', 'App\\Api\\V1\\Controllers\\ServiceRequest\\SaveServiceRequestRecommendationController@update');
    
            //Preferences
            $api->post('{hash}/preferences', 'App\\Api\\V1\\Controllers\\ServiceRequest\\SaveServiceRequestPreferencesController@save');
    
            //Service Request Source Property Filter
            $api->get('{hash}/property/preferences', 'App\\Api\\V1\\Controllers\\ServiceRequest\\ServiceRequestPropertyPrefrencesController@getData');
    
            $api->put('{hash}/sell', 'App\\Api\\V1\\Controllers\\ServiceRequest\\SaveServiceRequestController@saveListingId');
    
            // $api->post('/{hash}/call', 'App\\Api\\V1\\Controllers\\ServiceRequest\\CallServiceRequestController@default');
            // $api->post('/{hash}/call/{proxy}', 'App\\Api\\V1\\Controllers\\ServiceRequest\\CallServiceRequestController@proxy');
            
            //Get All Search Tags
            $api->get('/search/tags', 'App\\Api\\V4\\Controllers\\Search\\GetSearchTagController@getAll');
    
            //Share Emails By SR
            $api->post('{hash}/share', 'App\\Api\\V4\\Controllers\\Share\\GroupEmailController@send');
        });
    });
});