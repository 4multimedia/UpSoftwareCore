<?php

namespace Upsoftware\Core\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Upsoftware\Core\Classes\Core;
use Upsoftware\Core\Classes\Media;
use Upsoftware\Core\Facades\Core as CoreFacade;
use Upsoftware\Core\Facades\Media as MediaFacade;

class CoreServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        include __DIR__.'/../Http/helpers.php';

        $this->publishes([
            dirname(__DIR__).'/Config/concord.php'       => config_path('concord.php'),
            dirname(__DIR__).'/Config/upsoftware.php'       => config_path('upsoftware.php'),
            dirname(__DIR__).'/Config/hashids.php'       => config_path('hashids.php'),
        ], 'upsoftware');

        Route::aliasMiddleware('locale', \Upsoftware\Core\Http\Middleware\LocaleMiddleware::class);
    }

    public function register(): void
    {
        $this->registerFacades();
        $this->registerCommands();
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Upsoftware\Core\Console\Commands\UpsoftwareInstall::class,
                \Upsoftware\Core\Console\Commands\UpsoftwareMakeModule::class,
                \Upsoftware\Core\Console\Commands\UpsoftwareMakeController::class,
                \Upsoftware\Core\Console\Commands\UpsoftwareMakeModel::class,
            ]);
        }
    }

    protected function registerFacades(): void
    {
        $loader = AliasLoader::getInstance();

        $loader->alias('core', CoreFacade::class);
        $this->app->singleton('core', function () {
            return app()->make(Core::class);
        });

        $loader->alias('media', MediaFacade::class);
        $this->app->singleton('media', function () {
            return app()->make(Media::class);
        });
    }
}
