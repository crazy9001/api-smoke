<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 10:09 AM
 */

namespace Vtv\Base\Providers;

use Illuminate\Support\ServiceProvider;
use Vtv\Auth\Providers\AuthServiceProvider;
use Vtv\Question\Providers\QuestionServiceProvider;
use Vtv\Users\Providers\UsersServiceProvider;
use Vtv\News\Providers\NewsServiceProvider;
use Vtv\Menu\Providers\MenuServiceProvider;
use Vtv\Media\Providers\MediaServiceProvider;
use Vtv\Base\Supports\Helper;
use Vtv\Videos\Providers\VideosServiceProvider;

class BaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(UsersServiceProvider::class);
        $this->app->register(NewsServiceProvider::class);
        $this->app->register(MenuServiceProvider::class);
        $this->app->register(MediaServiceProvider::class);
        $this->app->register(QuestionServiceProvider::class);
        $this->app->register(VideosServiceProvider::class);

        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        $this->mergeConfigFrom(__DIR__ . '/../../config/cms.php', 'cms');
    }

    public function register()
    {
        Helper::autoload(__DIR__ . '/../../helpers');
    }

}