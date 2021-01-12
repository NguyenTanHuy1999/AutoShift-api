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
        $api->post('shop/register', [
            'as' => 'shop.register',
            'uses' => 'ShopController@registerShop',
        ]);


        // need authentication
        $api->group(['middleware' => ['api.auth']], function ($api) {
            $api->get('shop/edit', [
                'as' => 'shop.edit_get',
                'uses' => 'ShopController@editShop',
            ]);
//            $api->post('shop/edit', [
//                'as' => 'shop.edit_post',
//                'uses' => 'ShopController@editShop',
//            ]);
            $api->get('shop/list', [
                'as' => 'shop.list',
                'uses' => 'ShopController@viewShop',
            ]);
            $api->get('shop/delete', [
                'as' => 'shop.delete',
                'uses' => 'ShopController@deleteShop',
            ]);

            $api->group(['middleware' => ['manager.role']], function ($api) {
                $api->post('shop/edit', [
                    'as' => 'shop.edit_post',
                    'uses' => 'ShopController@editShop',
                ]);
            });
        });


        $api->post('shop/view', [
            'as' => 'shop.view',
            'uses' => 'ShopController@viewShop',
        ]);
    });
        


});
