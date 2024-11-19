<?php

    namespace Upsoftware\Core\Providers;

    use Konekt\Concord\BaseModuleServiceProvider;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\File;
    use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
    use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

    class CoreModuleServiceProvider extends BaseModuleServiceProvider {

        public function registerCustomRoutes(): void
        {
            $directory = $this->getBasePath()."/".$this->convention->routesFolder();

            $exclude_domain = $this->config('tenancy_exclude_domain', []);
            $middleware = ['api', 'locale'];
            if (config('upsoftware.tenancy', false)) {
                $middleware[] = InitializeTenancyByDomain::class;
                $middleware[] = PreventAccessFromCentralDomains::class;
            }

            if (is_dir($directory)) {
                $files = File::files($directory);
                foreach ($files as $file) {
                    $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    if ($fileName === 'api') {
                        Route::middleware($middleware)
                            ->namespace($this->getNamespaceRoot() . "\\Http\\Controllers")
                            ->as('api.' . $this->shortName() . '.')
                            ->prefix(config('upsoftware.api_prefix').$this->shortName())
                            ->group($file);
                    } else {
                        Route::namespace($this->getNamespaceRoot() . "\\Http\\Controllers")
                            ->as($this->shortName() . '.')
                            ->group($file);
                    }
                }
            }
        }
        public function boot(): void
        {

            if ($this->areMigrationsEnabled()) {
                $this->registerMigrations();
            }

            if ($this->areModelsEnabled()) {
                $this->registerModels();
                $this->registerEnums();
                $this->registerRequestTypes();
            }

            if ($this->areViewsEnabled()) {
                $this->registerViews();
            }

            $this->registerCustomRoutes();
            $this->loadTranslationsFrom($this->getBasePath().'/resources/lang/', $this->shortName());
        }
    }
