<?php

    namespace Upsoftware\Core\Providers;

    use Konekt\Concord\BaseModuleServiceProvider;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\File;

    class CoreModuleServiceProvider extends BaseModuleServiceProvider {

        public function registerCustomRoutes(): void
        {
            $directory = $this->getBasePath()."/".$this->convention->routesFolder();

            if (is_dir($directory)) {
                $files = File::files($directory);
                foreach ($files as $file) {
                    $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    if ($fileName === 'api') {
                        Route::middleware(['api', 'locale'])
                            ->namespace($this->getNamespaceRoot() . "\\Http\\Controllers")
                            ->as('api.' . $this->shortName() . '.')
                            ->prefix(config('upsoftware.api_prefix')."/".$this->shortName())
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
