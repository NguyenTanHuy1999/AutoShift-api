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
    $api->group(['middleware' => ['api.locale']], function ($api) {
        //Login
        $api->post('empshift/register', [
            'as' => 'empshift.register',
            'uses' => 'EmpshiftController@registerEF',
        ]);
        /*$api->post('empshift/del', [
            'as' => 'empshift.del',
            'uses' => 'EmpshiftController@delEF',
        ]);*/
        $api->post('empshift/edit', [
            'as' => 'empshift.edit',
            'uses' => 'EmpshiftController@editEF',
        ]);
        $api->get('empshift/list', [
            'as' => 'empshift.list',
            'uses' => 'EmpshiftController@viewEF',
        ]);
        $api->get('empshift/listbyuser', [
            'as' => 'shift.listbyuser',
            'uses' => 'EmpshiftController@listShiftbyUser'
        ]);
        $api->post('empshift/list-time-sheet', [
            'as' => 'shift.listShiftTimeSheet',
            'uses' => 'EmpshiftController@listShiftTimeSheet'
        ]);
    });
});
