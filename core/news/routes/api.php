<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 2:52 PM
 */
Route::group(['namespace' => 'Vtv\News\Http\Controllers', 'prefix' => 'api', 'middleware' => 'api'], function () {

    Route::group([ 'prefix' => 'news'], function () {

        Route::get('/list', [
            'middleware' => 'role:Secretary|Editor|Reporter,'.config('cms.permission_system.list_new'),
            'as' => 'api.list.news',
            'uses' => 'NewsController@listNews'
        ]);

        Route::post('/store', [
            'middleware' => 'role:Secretary|Editor|Reporter,'.config('cms.permission_system.add_new_news'),
            'as' => 'api.create.news',
            'uses' => 'NewsController@store'
        ]);

        Route::get('/detail', [
            'middleware' => 'role:Secretary|Editor|Reporter,'.config('cms.permission_system.edit_news'),
            'as' => 'api.detail.news',
            'uses' => 'NewsController@detailArticle'
        ]);

        Route::post('/changeStatus', [
            'middleware' => 'role:Secretary|Editor|Reporter,'.config('cms.permission_system.edit_news'),
            'as' => 'api.changeStatus.news',
            'uses' => 'NewsController@changeStatus'
        ]);

        Route::post('/receiverNew', [
            'middleware' => 'role:Secretary|Editor|Reporter,'.config('cms.permission_system.edit_news'),
            'as' => 'api.receiver.news',
            'uses' => 'NewsController@receiverNew'
        ]);

        Route::post('/update', [
            'middleware' => 'role:Secretary|Editor|Reporter,'.config('cms.permission_system.edit_news'),
            'as' => 'api.update.news',
            'uses' => 'NewsController@update'
        ]);

        Route::post('/update/highlight', [
            'middleware' => 'role:Secretary,'.config('cms.permission_system.edit_news'),
            'as' => 'api.update.highlight',
            'uses' => 'NewsController@updateHighLight'
        ]);

        Route::post('/update/featured', [
            'middleware' => 'role:Secretary,'.config('cms.permission_system.edit_news'),
            'as' => 'api.update.featured',
            'uses' => 'NewsController@updateFeatured'
        ]);

        Route::group([ 'prefix' => 'publish',  'middleware' => ['cors', 'auth.apikey']], function () {

            Route::get('/', [
                'as' => 'api.publish.list.new',
                'uses' => 'NewsController@getNewsPublishByCategory'
            ]);

            Route::get('/highlight', [
                'as' => 'api.publish.highlight.new',
                'uses' => 'NewsController@getNewsHighlightsPublish'
            ]);

            Route::get('/detail', [
                'as' => 'api.publish.detail.new',
                'uses' => 'NewsController@getNewsDetailPublish'
            ]);


            Route::get('/filter', [
                'as' => 'api.publish.filter.new',
                'uses' => 'NewsController@getNewsFilterPublish'
            ]);
        });

    });


    Route::group([ 'prefix' => 'categories'], function () {

        Route::get('/list', [
            'middleware' => 'role:Secretary,'.config('cms.permission_system.list_categories'),
            'as' => 'api.list.category',
            'uses' => 'CategoriesController@listCategories'
        ]);

        Route::post('/store', [
            'middleware' => 'role:Secretary,'.config('cms.permission_system.add_new_category'),
            'as' => 'api.create.categories',
            'uses' => 'CategoriesController@store'
        ]);

        Route::get('/detail', [
            'middleware' => 'role:Secretary,'.config('cms.permission_system.edit_category'),
            'as' => 'api.detail.category',
            'uses' => 'CategoriesController@detail'
        ]);

        Route::post('/update', [
            'middleware' => 'role:Secretary,'.config('cms.permission_system.edit_category'),
            'as' => 'api.update.categories',
            'uses' => 'CategoriesController@update'
        ]);

        Route::get('/get', [
            'middleware' => 'role:Secretary|Editor|Reporter,'.config('cms.permission_system.edit_news'),
            'as' => 'api.get.category',
            'uses' => 'CategoriesController@getCategories'
        ]);


        Route::group([ 'prefix' => 'publish',  'middleware' => ['cors', 'auth.apikey']], function () {

            Route::get('/', [
                'as' => 'api.public.category',
                'uses' => 'CategoriesController@getPublicCategories'
            ]);

        });

    });

    Route::group([ 'prefix' => 'program', 'middleware' => ['cors', 'auth.apikey']], function () {

        Route::get('/schedule', [
            'as' => 'api.public.program.schedule',
            'uses' => 'ProgramController@index'
        ]);


    });

});