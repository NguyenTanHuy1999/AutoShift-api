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
        $api->post('dep/register', [
            'as' => 'dep.register',
            'uses' => 'DepController@registerDep',
        ]);
        $api->post('dep/update', [
            'as' => 'dep.update',
            'uses' => 'DepController@updateDep',
        ]);
        $api->get('dep/list', [
            'as' => 'dep.list',
            'uses' => 'DepController@listDep',
        ]);
        $api->get('dep/list1', [
            'as' => 'dep.list1',
            'uses' => 'DepController@list',
        ]);
        $api->get('dep/delete', [
            'as' => 'dep.delete',
            'uses' => 'DepController@deleteDep',
        ]);
    });



});
