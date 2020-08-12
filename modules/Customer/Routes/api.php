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

    // Unauthenticated OR Guest endpoints
    $api->group(['middleware' => ['guest']], function(Router $api) {

        // Customer Endpoint
        $api->group(['prefix' => 'customer'], function(Router $api) {
            // Validate Customer's username
            $api->get('exists', 'Modules\\Customer\\Http\\Controllers\\Frontend\\Customer\\CustomerAuthController@exists');

            // Register Customer
            $api->post('register', 'Modules\\Customer\\Http\\Controllers\\Frontend\\Customer\\CustomerAuthController@register');

            // Authentication
            $api->post('login', 'Modules\\Customer\\Http\\Controllers\\Frontend\\Customer\\CustomerAuthController@login');

            //Social Login
            $api->get('login/{social}', 'Modules\\Customer\\Http\\Controllers\\Frontend\\Customer\\CustomerSocialAuthController@redirectToProvider');
            $api->any('login/{social}/callback', 'Modules\\Customer\\Http\\Controllers\\Frontend\\Customer\\CustomerSocialAuthController@handleProviderCallback');

            // Password Management
            $api->post('forgot', 'Modules\\Customer\\Http\\Controllers\\Frontend\\Customer\\CustomerAuthController@forgotPassword');
            $api->post('reset', 'Modules\\Customer\\Http\\Controllers\\Frontend\\Customer\\CustomerAuthController@resetPassword');
        });
    });

    // Authenticated Endpoints for Frontend
    $api->group(['middleware' => ['auth:frontend']], function(Router $api) {

        // Customer Endpoints
        $api->group(['prefix' => 'customer'], function(Router $api) {
            // Logout
            $api->put('logout', 'Modules\\Customer\\Http\\Controllers\\Frontend\\Customer\\CustomerAuthController@logout');

            // Password Management
            $api->put('changepass', 'Modules\\Customer\\Http\\Controllers\\Frontend\\Auth\\ChangePasswordController@changePassword');

            // Customer Management
            $api->get('/', 'Modules\\Customer\\Http\\Controllers\\Frontend\\Customer\\CustomerController@show');
            $api->put('{hash}', 'Modules\\Customers\\Http\\Controllers\\Frontend\\Customer\\CustomerController@update');
        });
    });
});