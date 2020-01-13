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
Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});
Route::group([
    'prefix' => 'patients'
], function ($router) {
    Route::get('/', 'PatientController@index');
    Route::get('/{id}', 'PatientController@show');
    Route::post('', 'PatientController@store');
    Route::put('/{id}', 'PatientController@update');
    Route::delete('/{id}', 'PatientController@delete');
});
