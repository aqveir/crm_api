<?php

use Illuminate\Http\RedirectResponse;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Reset Password Link For CRM Users
Route::get('reset/{token}', [
    'as' => 'password.reset',
    'uses' => 'Backend\\Auth\\UserAuthController@resetPassword'
])->name('user.password.reset');
