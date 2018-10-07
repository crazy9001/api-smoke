<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 8/21/2018
 * Time: 11:51 AM
 */
Route::group(['namespace' => 'Vtv\Media\Http\Controllers', 'prefix' => 'api', 'middleware' => 'api'], function () {

    Route::group([ 'prefix' => 'media'], function () {

        Route::get('/gallery', [
            'middleware' => 'role:Secretary|Editor|Reporter,'.config('cms.permission_system.media_manager'),
            'as' => 'api.files.gallery.show',
            'uses' => 'MediaController@getGallery'
        ]);

    });

    Route::group(['prefix' => 'files'], function () {

        Route::post('/edit', [
            'middleware' => 'role:Secretary|Editor|Reporter,'.config('cms.permission_system.media_manager'),
            'as' => 'files.store',
            'uses' => 'FileController@postEdit',
        ]);

        Route::get('/detail', [
            'middleware' => 'role:Secretary|Editor|Reporter,'.config('cms.permission_system.media_manager'),
            'as' => 'files.detail',
            'uses' => 'FileController@fileDetail',
        ]);

    });

});