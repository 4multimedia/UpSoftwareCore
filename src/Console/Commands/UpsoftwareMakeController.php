<?php

namespace Upsoftware\Core\Console\Commands;

class UpsoftwareMakeController extends UpsoftwareStub
{
    public $moduleName;
    protected $signature = 'upsoftware:make.controller {name}';

    public function handle()
    {
        $name = $this->argument('name');
        $this->moduleName = $this->ask('Module name:');
        $this->createSourceFromStub('Controller.stub', 'Http/Controllers', $name);
    }
}
