<?php

namespace Upsoftware\Core\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

use Upsoftware\Core\Classes\Core;
use Upsoftware\Core\Facades\Core as CoreFacade;

class CoreServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        include __DIR__.'/../Http/helpers.php';

        $this->publishes([
            dirname(__DIR__).'/Config/concord.php'       => config_path('concord.php'),
            dirname(__DIR__).'/Config/upsoftware.php'       => config_path('upsoftware.php'),
        ], 'upsoftware');
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
                \Upsoftware\Core\Console\Commands\UpsoftwareInstall::class
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
    }
}
