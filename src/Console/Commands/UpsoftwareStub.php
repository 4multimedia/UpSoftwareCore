<?php

namespace Upsoftware\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class UpsoftwareStub extends Command
{
    public $moduleName;
    public $pathName;
    protected $files;

    public function __construct(Filesystem $files) {
        parent::__construct();
        $this->files = $files;
    }

    private function convertSlashes($path): string
    {
        return str_replace(['/', '\\'], '\\', $path);
    }

    protected function createSourceFromStub($type, $directory, $name = ""): void
    {
        $fileName = $type;
        if($name) {
            $names = explode("/", $name);
            $fileName = $names[count($names) - 1];
            unset($names[count($names) - 1]);
            $this->pathName = $this->convertSlashes(implode("/", $names));
        }

        $stubPath = dirname(__DIR__)."/../resources/stubs/{$type}";
        if (!$this->files->exists($stubPath)) {
            $this->error("Stub file for {$type} does not exist!");
        }

        $stubContent = $this->files->get($stubPath);
        $stubContent = strtr($stubContent, [
            '{{ namespace }}' => $this->getNamespace($directory),
            '{{ classname }}' => $fileName
        ]);

        $filePath = app_path("/Modules/".ucfirst($this->moduleName)."/".$directory.($this->pathName ? "/".$this->pathName : "")."/".strtr($fileName, ['.stub' => '']).".php");
        $this->makeDirectory($filePath);
        $this->files->put($filePath, $stubContent);

        $this->info("{$type} created successfully at {$filePath}");
    }

    protected function makeDirectory($path): void
    {
        $directory = dirname($path);

        if (!$this->files->exists($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }

    protected function getNamespace($directory): string
    {
        return "App\\Modules\\" . ucfirst($this->moduleName). "\\". $this->convertSlashes(ucfirst($directory)).($this->pathName ? "\\".$this->pathName : "");
    }
}
