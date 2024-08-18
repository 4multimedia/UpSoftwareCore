<?php

namespace Upsoftware\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class UpsoftwareInstall extends Command
{
    protected $signature = 'upsoftware:install { --force : Overwrite any existing files }';

    public function handle()
    {
        $this->info('Publish configurations');
        Artisan::call('vendor:publish --tag=upsoftware');
    }
}
