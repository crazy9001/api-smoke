<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 9/24/2018
 * Time: 3:01 PM
 */

Route::group(['namespace' => 'Vtv\Question\Http\Controllers', 'prefix' => 'api', 'middleware' => 'api'], function () {

    Route::group([ 'prefix' => 'topic'], function () {

        Route::post('/store', [
            'middleware' => 'role:Secretary,'.config('cms.permission_system.add_new_topic'),
            'as' => 'api.topic.news',
            'uses' => 'TopicController@store'
        ]);

    });

    Route::group([ 'prefix' => 'question'], function () {

        Route::post('/store', [
            'middleware' => 'role:Secretary,'.config('cms.permission_system.add_new_question'),
            'as' => 'api.create.question',
            'uses' => 'QuestionController@store'
        ]);

        Route::group([ 'prefix' => 'publish',  'middleware' => ['cors', 'auth.apikey']], function () {

            Route::get('/', [
                'as' => 'api.publish.list.question',
                'uses' => 'QuestionController@publicListQuestion'
            ]);

        });

    });

    Route::group([ 'prefix' => 'answer'], function () {

        Route::post('/store', [
            'middleware' => 'role:Secretary,'.config('cms.permission_system.add_new_answer'),
            'as' => 'api.create.answer',
            'uses' => 'AnswerController@store'
        ]);
    });

});