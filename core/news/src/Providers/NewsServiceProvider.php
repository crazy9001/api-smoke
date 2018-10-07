<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 2:50 PM
 */

namespace Vtv\News\Providers;

use Illuminate\Support\ServiceProvider;
use Vtv\News\Models\NewsAttribute;
use Vtv\News\Repositories\Eloquent\DbNewsAttributeRepository;
use Vtv\News\Repositories\Eloquent\DbNewsRepository;
use Vtv\News\Repositories\Interfaces\NewAttributeInterface;
use Vtv\News\Repositories\Interfaces\NewInterface;
use Vtv\News\Repositories\Eloquent\DbCategoriesRepository;
use Vtv\News\Repositories\Interfaces\CategoriesInterface;
use Vtv\News\Models\News;
use Vtv\News\Models\Categories;

class NewsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        $this->mergeConfigFrom(__DIR__ . '/../../config/news.php', 'news');
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'news');
    }

    public function register()
    {
        $this->app->singleton(NewInterface::class, function () {
            return new DbNewsRepository(new News());
        });

        $this->app->singleton(CategoriesInterface::class, function () {
            return new DbCategoriesRepository(new Categories());
        });

        $this->app->singleton(NewAttributeInterface::class, function () {
            return new DbNewsAttributeRepository(new NewsAttribute());
        });

    }

}