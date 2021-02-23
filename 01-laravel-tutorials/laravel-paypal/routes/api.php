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

Route::post('/paypal-transaction-complete', function (Request $request) {
    return response()->json($request);
});

Route::post('/paypal-create-product', 'PaypalController@createProduct');

Route::post('/paypal-create-plan', 'PaypalController@createPlan');

Route::get('/paypal-get-token', 'PaypalController@getToken');

