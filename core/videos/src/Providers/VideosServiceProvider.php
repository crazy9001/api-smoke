<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 10/6/2018
 * Time: 1:43 PM
 */

namespace Vtv\Videos\Providers;

use Illuminate\Support\ServiceProvider;
use Vtv\Videos\Models\Element;
use Vtv\Videos\Models\Timeline;
use Vtv\Videos\Models\Videos;
use Vtv\Videos\Models\VideoSeo;
use Vtv\Videos\Repositories\Eloquent\DbVideosElementRepository;
use Vtv\Videos\Repositories\Eloquent\DbVideosRepository;
use Vtv\Videos\Repositories\Eloquent\DbVideosSeoRepository;
use Vtv\Videos\Repositories\Eloquent\DbVideosTimelineRepository;
use Vtv\Videos\Repositories\Interfaces\VideoElementInterface;
use Vtv\Videos\Repositories\Interfaces\VideoInterface;
use Vtv\Videos\Repositories\Interfaces\VideoSeoInterface;
use Vtv\Videos\Repositories\Interfaces\VideoTimelineInterface;

class VideosServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
    }

    public function register()
    {
        $this->app->singleton(VideoInterface::class, function () {
            return new DbVideosRepository(new Videos());
        });

        $this->app->singleton(VideoElementInterface::class, function () {
            return new DbVideosElementRepository(new Element());
        });

        $this->app->singleton(VideoTimelineInterface::class, function () {
            return new DbVideosTimelineRepository(new Timeline());
        });

        $this->app->singleton(VideoSeoInterface::class, function () {
            return new DbVideosSeoRepository(new VideoSeo());
        });
    }

}