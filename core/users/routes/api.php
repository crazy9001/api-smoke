<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 12:53 PM
 */
Route::group(['namespace' => 'Vtv\Users\Http\Controllers', 'prefix' => 'api', 'middleware' => 'api'], function () {

    Route::group([ 'prefix' => 'users'], function () {

        Route::get('/list', [
            'middleware' => 'role:Secretary|Editor,'.config('cms.permission_system.list_user'),
            'as' => 'api.list.users',
            'uses' => 'IndexController@index'
        ]);

        Route::post('/store', [
            'middleware' => 'role:Secretary,'.config('cms.permission_system.add_new_user'),
            'as' => 'api.create.user',
            'uses' => 'IndexController@store'
        ]);

        Route::get('/information', [
            'middleware' => 'role:Secretary|Editor,'.config('cms.permission_system.view_detail_user'),
            'as' => 'api.detail.users',
            'uses' => 'IndexController@detailUser'
        ]);

        Route::post('/update', [
            'middleware' => 'role:Secretary,'.config('cms.permission_system.view_detail_user'),
            'as' => 'api.update.user',
            'uses' => 'IndexController@update'
        ]);

        Route::post('/token', [
            'middleware'=> 'jwt.auth',
            'as' => 'api.check.token',
            'uses' => 'IndexController@checkToken'
        ]);

    });


});