$api<?php

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
Route::group(['prefix' => 'auth'], function ($api) {
    $api->post('register', 'AuthController@register');
    $api->post('login', 'AuthController@login');
});
Route::group(['middleware' => 'jwt.auth'], function ($api) {
    $api->group(['prefix' => 'auth'], function ($api) {
        $api->post('logout', 'AuthController@logout');
        $api->post('refresh', 'AuthController@refresh');
        $api->post('me', 'AuthController@me');
    });
    $api->group(['prefix' => 'patients'], function ($api) {
        $api->get('/', 'PatientController@index');
        $api->get('/{id}', 'PatientController@show');
        $api->post('', 'PatientController@store');
        $api->put('/{id}', 'PatientController@update');
        $api->delete('/{id}', 'PatientController@delete');
    });
    $api->group(['prefix' => 'appointments'], function ($api) {
        $api->get('/', 'AppointmentController@index');
        $api->get('/{id}', 'AppointmentController@show');
        $api->post('', 'AppointmentController@store');
        $api->put('/{id}', 'AppointmentController@update');
        $api->delete('/{id}', 'AppointmentController@delete');
    });
});
