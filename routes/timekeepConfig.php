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

        $api->post('timekeep/register', [
            'as' => 'timekeep.register',
            'uses' => 'TimekeepConfigController@register',
        ]);
        $api->post('timekeep/update', [
            'as' => 'timekeep.update',
            'uses' => 'TimekeepConfigController@update',
        ]);
        $api->get('timekeep/detail', [
            'as' => 'timekeep.detail',
            'uses' => 'TimekeepConfigController@detail',
        ]);
        $api->get('timekeep/delete', [
            'as' => 'timekeep.delete',
            'uses' => 'TimekeepConfigController@delete',
        ]);
        $api->get('timekeep/list', [
            'as' => 'timekeep.list',
            'uses' => 'TimekeepConfigController@list',
        ]);
    });
});
