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
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->get('users/{id}', 'App\Http\Controllers\Api\TestController@test');
    $api->get('get-token-pwd', 'App\Http\Controllers\Api\TestController@getTokenByEmailAndPwd');
    $api->get('get-token-obj', 'App\Http\Controllers\Api\TestController@getTokenByObj');
    $api->get('get-token-custom', 'App\Http\Controllers\Api\TestController@getTokenCustm');
    $api->post('authorization', 'App\Http\Controllers\Api\TestController@authorization');
});

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:api');
