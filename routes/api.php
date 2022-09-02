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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'API\UserController@login');
Route::group(['middleware' => 'auth:api'], function() {
    Route::post('details', 'API\UserController@details');
    Route::post('logout', 'API\UserController@logout');


    Route::post('transportedfile', 'API\RpaController@transportedFile');
    Route::post('saveresults', 'API\RpaController@saveResults');


    Route::post('saveData', 'API\RpaController@saveFlow');
    Route::post('callrpa', 'API\RpaController@callRpa');
    Route::get('callRpaById/{id}', 'API\RpaController@callRpaById');

});
