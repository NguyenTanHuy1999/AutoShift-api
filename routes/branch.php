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
        $api->post('branch/register', [
            'as' => 'branch.register',
            'uses' => 'BranchController@registerBranch',
        ]);
        $api->post('branch/del', [
            'as' => 'branch.del',
            'uses' => 'BranchController@delBranch',
        ]);
        $api->post('branch/update', [
            'as' => 'branch.update',
            'uses' => 'BranchController@updateBranch',
        ]);
        $api->get('branch/detail', [
            'as' => 'branch.detail',
            'uses' => 'BranchController@detailBranch',
        ]);
        $api->get('branch/delete', [
            'as' => 'branch.delete',
            'uses' => 'BranchController@deleteBranch',
        ]);
        $api->get('branch/list', [
            'as' => 'branch.list',
            'uses' => 'BranchController@listBranch',
        ]);
        $api->get('branch/list1', [
            'as' => 'branch.list1',
            'uses' => 'BranchController@list',
        ]);
    });



});
