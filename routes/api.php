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
Route::group(['middleware'=> 'cors'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('register', 'AuthController@register');
        Route::post('login', 'AuthController@login');
    });
    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('logout', 'AuthController@logout');
            Route::post('refresh', 'AuthController@refresh');
            Route::post('me', 'AuthController@me');
        });
        Route::group(['prefix' => 'patients'], function () {
            Route::get('/', 'PatientController@index');
            Route::get('/{id}', 'PatientController@show');
            Route::post('', 'PatientController@store');
            Route::put('/{id}', 'PatientController@update');
            Route::delete('/{id}', 'PatientController@delete');
        });
        Route::group(['prefix' => 'appointments'], function () {
            Route::get('/', 'AppointmentController@index');
            Route::get('/{id}', 'AppointmentController@show');
            Route::post('', 'AppointmentController@store');
            Route::put('/{id}', 'AppointmentController@update');
            Route::delete('/{id}', 'AppointmentController@delete');
        });
    });
});
