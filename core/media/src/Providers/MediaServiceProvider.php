<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 8/21/2018
 * Time: 11:49 AM
 */

namespace Vtv\Media\Providers;

use Illuminate\Support\ServiceProvider;
use Vtv\Media\Repositories\Interfaces\FileInterface;
use Vtv\Media\Repositories\Interfaces\FolderInterface;
use Vtv\Media\Repositories\Eloquent\FileRepository;
use Vtv\Media\Repositories\Eloquent\FolderRepository;
use Vtv\Media\Models\File;
use Vtv\Media\Models\Folder;

class MediaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(FileInterface::class, function () {
            return new FileRepository(new File());
        });
        $this->app->singleton(FolderInterface::class, function () {
            return new FolderRepository(new Folder());
        });

    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        $this->mergeConfigFrom(__DIR__ . '/../../config/media.php', 'media');
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'media');
    }
}