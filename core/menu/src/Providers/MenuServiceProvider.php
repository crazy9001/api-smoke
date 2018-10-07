<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 8/13/2018
 * Time: 2:53 PM
 */

namespace Vtv\Menu\Providers;

use Illuminate\Support\ServiceProvider;
use Vtv\Menu\Repositories\Interfaces\MenuInterface;
use Vtv\Menu\Repositories\Eloquent\DbMenuRepository;
use Vtv\Menu\Models\Menu;

class MenuServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        $this->mergeConfigFrom(__DIR__ . '/../../config/menu.php', 'menu');
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'menu');
    }
    public function register()
    {
        $this->app->singleton(MenuInterface::class, function () {
            return new DbMenuRepository(new Menu());
        });
    }
}