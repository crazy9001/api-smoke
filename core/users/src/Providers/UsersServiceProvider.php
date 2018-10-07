<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 10:44 AM
 */

namespace Vtv\Users\Providers;

use Illuminate\Support\ServiceProvider;
use Vtv\Users\Models\User;
use Vtv\Users\Repositories\Interfaces\UserInterface;
use Vtv\Users\Repositories\Eloquent\DbUsersRepository;

class UsersServiceProvider extends ServiceProvider
{
    /**
     *
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'users');
    }

    /**
     *
     */
    public function register()
    {
        $this->app->singleton(UserInterface::class, function () {
            return new DbUsersRepository(new User());
        });
    }

}