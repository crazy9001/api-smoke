<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 10/6/2018
 * Time: 1:44 PM
 */

Route::group(['namespace' => 'Vtv\Videos\Http\Controllers', 'prefix' => 'api', 'middleware' => 'api'], function () {

    Route::group([ 'prefix' => 'videos'], function () {

        Route::post('/store', [
            'middleware' => 'role:Secretary,'.config('cms.permission_system.add_new_user'),
            'as' => 'api.create.video',
            'uses' => 'IndexController@store'
        ]);

        Route::get('/draft', [
            'middleware' => 'role:Secretary|Editor|Reporter,'.config('cms.permission_system.list_new'),
            'as' => 'api.list.video.draft',
            'uses' => 'IndexController@getListVideosDraft'
        ]);

        Route::get('/publish', [
            'middleware' => 'role:Secretary|Editor|Reporter,'.config('cms.permission_system.list_new'),
            'as' => 'api.list.video.publish',
            'uses' => 'IndexController@getListVideosPublish'
        ]);

    });

});