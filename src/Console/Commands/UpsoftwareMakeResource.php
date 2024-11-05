<?php

namespace Upsoftware\Core\Console\Commands;

use Illuminate\Support\Facades\File;

class UpsoftwareMakeResource extends UpsoftwareStub
{
    public $moduleName;
    protected $signature = 'upsoftware:make.resource {name}';

    public function handle()
    {
        $name = $this->argument('name');
        $this->moduleName = $this->ask('Module name:');
        $this->createSourceFromStub('Resource.stub', 'Http/Resources', $name);
    }
}
