<?php

namespace Upsoftware\Core\Console\Commands;

use Illuminate\Support\Facades\File;

class UpsoftwareMakeModule extends UpsoftwareStub
{
    public $moduleName;
    protected $signature = 'upsoftware:make.module {name}';

    public function handle()
    {
        $this->info('Creating UpSofware module...');
        $this->moduleName= $this->argument('name');

        $directories = [
            "Console/Commands",
            "Contracts",
            "Events",
            "Exceptions",
            "Factories",
            "Helpers",
            "Http/Controllers",
            "Http/Middleware",
            "Http/Requests",
            "Http/Resources",
            "Jobs",
            "Listeners",
            "Models",
            "Notifications",
            "Providers",
            "Services",
            "Tests/Feature",
            "Tests/Unit",
            "resources/assets",
            "resources/config",
            "resources/database/migrations",
            "resources/database/seeds",
            "resources/lang",
            "resources/views",
            "resources/routes",
        ];

        foreach ($directories as $directory) {
            $path = app_path("/Modules/".ucfirst($this->moduleName)."/".$directory);
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
                $this->info("Created: {$path}");
            } else {
                $this->info("Directory already exists: {$path}");
            }
        }

        $this->createSourceFromStub('ModuleServiceProvider.stub', 'Providers');
        $this->createSourceFromStub('routes/api.stub', 'resources');
        $this->createSourceFromStub('routes/web.stub', 'resources');
    }
}
