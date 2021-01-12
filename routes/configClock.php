<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */
// $app->get('/', function () use ($app) {
//     return $app->version();
// });

$api = app('Dingo\Api\Routing\Router');

// v1 version API
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\V1'], function ($api) {
    $api->group(['middleware' => ['api.auth']], function ($api) {
        //Login
        $api->post('wifi/register', [
            'as' => 'wifi.register',
            'uses' => 'WifiClockConfigController@register',
        ]);
        $api->post('wifi/update', [
            'as' => 'wifi.update',
            'uses' => 'WifiClockConfigController@update',
        ]);
        $api->get('wifi/detail', [
            'as' => 'wifi.detail',
            'uses' => 'WifiClockConfigController@detail',
        ]);
        $api->get('wifi/delete', [
            'as' => 'wifi.delete',
            'uses' => 'WifiClockConfigController@delete',
        ]);
        $api->get('wifi/list', [
            'as' => 'wifi.list',
            'uses' => 'WifiClockConfigController@list',
        ]);
    });
});
