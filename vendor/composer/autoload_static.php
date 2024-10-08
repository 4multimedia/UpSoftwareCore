<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1df8fdb063f7451b46df4236d85f228b
{
    public static $prefixLengthsPsr4 = array (
        'U' => 
        array (
            'Upsoftware\\Core\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Upsoftware\\Core\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1df8fdb063f7451b46df4236d85f228b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1df8fdb063f7451b46df4236d85f228b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1df8fdb063f7451b46df4236d85f228b::$classMap;

        }, null, ClassLoader::class);
    }
}
