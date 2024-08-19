<?php

namespace Upsoftware\Core\Console\Commands;

use Illuminate\Support\Facades\File;

class UpsoftwareMakeModel extends UpsoftwareStub
{
    public $moduleName;
    protected $signature = 'upsoftware:make.model {name}';

    public function handle()
    {
        $name = $this->argument('name');
        $this->moduleName = $this->ask('Module name:');
        $this->createSourceFromStub('Model.stub', 'Models', $name);
    }
}
