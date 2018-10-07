<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 2:52 PM
 */
Route::group(['namespace' => 'Vtv\Menu\Http\Controllers', 'prefix' => 'api', 'middleware' => 'api'], function () {

    Route::group([ 'prefix' => 'menu'], function () {

        Route::get('/', [
            'middleware' => 'role:Secretary|Editor|Reporter,'.config('cms.permission_system.list_menu'),
            'as' => 'api.list.menu',
            'uses' => 'MenuController@index'
        ]);

        Route::post('/store', [
            'middleware' => 'role:Secretary,'.config('cms.permission_system.add_new_menu'),
            'as' => 'api.create.menu',
            'uses' => 'MenuController@store'
        ]);

        Route::group([ 'prefix' => 'publish'], function () {

            Route::get('/', [
                'middleware'    =>  'auth.apikey',
                'as' => 'api.publish.list.menu',
                'uses' => 'MenuController@publishListMenu'
            ]);

        });

    });

});