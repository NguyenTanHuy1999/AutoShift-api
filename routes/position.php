<?php
$api = app('Dingo\Api\Routing\Router');

// v1 version API
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\V1'], function ($api) {
    $api->group(['middleware' => ['api.locale']], function ($api) {
        //Login
        $api->post('position/register', [
            'as' => 'position.register',
            'uses' => 'PositionController@createPosition',
        ]);
        $api->get('position/delete', [
            'as' => 'position.delete',
            'uses' => 'PositionController@deletePosition',
        ]);
        $api->get('position/list', [
            'as' => 'position.list',
            'uses' => 'PositionController@listPosition',
        ]);
    });


});