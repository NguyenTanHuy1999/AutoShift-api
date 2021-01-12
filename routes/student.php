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
        $api->get('students/view-list', [
            'as' => 'student.viewList',
            'uses' => 'StudentController@viewList',
        ]);

        $api->get('students/create-form', [
            'as' => 'student.viewCreateFormGet',
            'uses' => 'StudentController@viewCreateForm',
        ]);

        $api->get('students/delete', [
            'as' => 'student.delete',
            'uses' => 'StudentController@delete',
        ]);

        $api->post('students/create-form', [
            'as' => 'student.viewCreateFormPost',
            'uses' => 'StudentController@viewCreateForm',
        ]);

    });
});
