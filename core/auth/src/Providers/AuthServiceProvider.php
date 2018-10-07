<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 10:12 AM
 */

namespace Vtv\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use Vtv\Auth\Http\Middleware\RoleAuthenticate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }

    public function register()
    {
        /**
         * @var Router $router
         */
        $router = $this->app['router'];
        $router->aliasMiddleware('role', RoleAuthenticate::class);

    }

}