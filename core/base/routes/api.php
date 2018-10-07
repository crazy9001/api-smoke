<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 9/5/2018
 * Time: 11:39 AM
 */

Route::group(['namespace' => 'Vtv\Base\Http\Controllers', 'prefix' => 'api', 'middleware' => 'api'], function () {

    Route::group([ 'prefix' => 'contact'], function () {

        Route::post('/send', [
            'as' => 'send.mail',
            'uses' => 'ContactController@sendMail',
        ]);

    });

});