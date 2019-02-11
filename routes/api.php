<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('v1/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'auth'], function () {

    Route::post('signin', 'Auth\AuthController@signin');
    Route::post('signup', 'Auth\AuthController@signup');

    Route::post('refreshtoken', 'Auth\AuthController@token_refresh');

    Route::group(['middleware' => 'auth:api'], function() {

        Route::post('user', 'Auth\AuthController@Me'); 
        Route::post('logout', 'Auth\AuthController@logout');

    });

});

Route::group(['middleware' => 'auth:api'], function() {

    Route::group(['prefix' => 'phonenumber'], function () {
        Route::get('all', 'PhoneNumberController@index');
        Route::get('show/{id}', 'PhoneNumberController@show');
        Route::post('store', 'PhoneNumberController@store');
        Route::post('update', 'PhoneNumberController@update');
        Route::post('delete', 'PhoneNumberController@delete');
    });
    
});
