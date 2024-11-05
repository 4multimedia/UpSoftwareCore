<?php

namespace Upsoftware\Core\Console\Commands;

use Illuminate\Support\Facades\File;

class UpsoftwareMakeRequest extends UpsoftwareStub
{
    public $moduleName;
    protected $signature = 'upsoftware:make.resquest {name}';

    public function handle()
    {
        $name = $this->argument('name');
        $this->moduleName = $this->ask('Module name:');
        $this->createSourceFromStub('Request.stub', 'Http/Requests', $name);
    }
}
