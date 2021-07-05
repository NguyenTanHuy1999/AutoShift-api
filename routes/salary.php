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
        $api->post('salary/register', [
            'as' => 'salary.register',
            'uses' => 'SalaryController@createSalary',
        ]);
        $api->post('salary/view', [
            'as' => 'salary.view',
            'uses' => 'SalaryController@viewSalary',
        ]);
        $api->post('salary/statistics', [
            'as' => 'salary.view',
            'uses' => 'SalaryController@viewTimeStatistics',
        ]);
        $api->post('salary/statistics1', [
            'as' => 'salary.view',
            'uses' => 'SalaryController@viewWhoIsWorking',
        ]);
        $api->post('salary/statistics2', [
            'as' => 'salary.view',
            'uses' => 'SalaryController@viewSalaryFund',
        ]);
    });
});
