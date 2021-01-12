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
        $api->post('auth/login', [
            'as' => 'auth.login',
            'uses' => 'AuthController@login',
        ]);

        //Get orgamization
        $api->get('auth/organization', [
            'as' => 'auth.organization',
            'uses' => 'AuthController@organization',
        ]);
        //test task schedule
        $api->get('user/mail', [
            'as' => 'user.mail',
            'uses' => 'UserController@sendMail',
        ]);
        //end test

        //Validate pre-login
        $api->post('auth/validate-pre-login', [
            'as' => 'auth.validatePreLogin',
            'uses' => 'AuthController@validatePreLogin',
        ]);

        //Login new
        $api->post('auth/login-new', [
            'as' => 'auth.loginNew',
            'uses' => 'AuthController@loginNew',
        ]);

        // AUTH
        // refresh jwt token
        $api->post('auth/token/refresh', [
            'as' => 'auth.token.refresh',
            'uses' => 'AuthController@refreshToken',
        ]);

        // need authentication
        $api->group(['middleware' => ['api.auth']], function ($api) {
            //Cần quyền manager
            $api->group(['middleware' => ['manager.role']], function ($api) {
                //Delete user
                $api->get('user/delete', [
                    'as' => 'user.delete',
                    'uses' => 'UserController@deleteUser',
                ]);
            });
            //
            $api->get('user', [
                'as' => 'user.show',
                'uses' => 'UserController@userShow',
            ]);

            //Update user    profile
            $api->post('user/update', [
                'as' => 'user.update',
                'uses' => 'UserController@update',
            ]);
            $api->get('user/update', [
                'as' => 'user.update',
                'uses' => 'UserController@update',
            ]);
            //Tạo user
            $api->post('user/create',[
                'as'=>'user.create',
                'uses'=>'UserController@createUser'
            ]);


            //Get Info User
            $api->get('user/info/{username?}', [
                'as' => 'user.info',
                'uses' => 'UserController@info',
            ]);


            //Get List User
            $api->get('user/list', [
                'as' => 'user.list',
                'uses' => 'UserController@list',
            ]);

            //Get Info User
            $api->get('user/company-field', [
                'as' => 'user.companyField',
                'uses' => 'UserController@companyField',
            ]);

        });
        //Create User

    });
        


});
