<?php

namespace Upsoftware\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class UpsoftwareInstall extends Command
{
    protected $signature = 'upsoftware:install { --force : Overwrite any existing files }';

    public function handle()
    {
        $this->info('Publish configurations');
        Artisan::call('vendor:publish --tag=upsoftware');
        $this->info('Create the symbolic link to storage');
        Artisan::call('storage:link');
        $this->info('Publish Translation Loader');
        Artisan::call('vendor:publish --provider=Spatie\TranslationLoader\TranslationServiceProvider');

        if (config('upsoftware.tenancy', false)) {
            Artisan::call('tenancy:install');
        }

        $this->info('Create hash configuration');
        core()->set_config('hashids', [
            'connections.main.salt' => Str::random(32),
            'connections.main.length' => 64,
            'connections.alternative.salt' => Str::random(32),
            'connections.alternative.length' => 64,
        ]);
    }
}
