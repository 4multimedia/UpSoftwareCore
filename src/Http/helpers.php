<?php

use Upsoftware\Core\Classes\Core;
use Upsoftware\Core\Classes\Media;
use Upsoftware\Core\Classes\ModelFilter;

if (! function_exists('core')) {
    /**
     * Core helper.
     */
    function core(): Core
    {
        return app('core');
    }
}

if (! function_exists('media')) {
    /**
     * Media helper.
     */
    function media(): Media
    {
        return app('media');
    }
}

if (! function_exists('set_config')) {
    function set_config(String $name, Array $settings): void
    {
        $filePath = config_path($name.'.php');
        $config = include $filePath;

        foreach ($settings as $key => $value) {
            $keys = explode('.', $key);

            $temp = &$config;
            foreach ($keys as $part) {
                if (!isset($temp[$part])) {
                    $temp[$part] = [];
                }
                $temp = &$temp[$part];
            }
            $temp = $value;
        }

        $configContent = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        $configContent = str_replace(['array (', ')'], ['[', ']'], $configContent);
        $configContent = preg_replace('/\[\s+\]/', '[]', $configContent);

        $configContent = preg_replace_callback('/^( +)/m', function($matches) {
            $spaces = strlen($matches[1]);
            $tabs = intdiv($spaces, 2);
            return str_repeat("\t", $tabs);
        }, $configContent);

        file_put_contents($filePath, $configContent);
    }
}


if (!function_exists('ModelFilter')) {
    function ModelFilter($modelClass) {
        return ModelFilter::make($modelClass);
    }
}
