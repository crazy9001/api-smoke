<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 10:28 AM
 */
Route::group(['namespace' => 'Vtv\Auth\Http\Controllers', 'prefix' => 'api', 'middleware' => 'api'], function () {

    Route::group([ 'prefix' => 'auth'], function () {

        Route::post('/login', [
            'as' => 'api.access.login',
            'uses' => 'AuthenticateController@login'
        ]);

        Route::get('/logout', [
            'as' => 'api.logout',
            'uses' => 'AuthenticateController@logout'
        ]);

    });


});