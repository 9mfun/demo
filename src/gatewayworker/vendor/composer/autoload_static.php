<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7acb7638ae12c1859ff0809170c44f23
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Workerman\\' => 10,
        ),
        'G' => 
        array (
            'GatewayWorker\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Workerman\\' => 
        array (
            0 => __DIR__ . '/..' . '/workerman/workerman',
        ),
        'GatewayWorker\\' => 
        array (
            0 => __DIR__ . '/..' . '/workerman/gateway-worker/src',
        ),
    );

    public static $fallbackDirsPsr4 = array (
        0 => __DIR__ . '/../..' . '/Applications/YourApp',
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7acb7638ae12c1859ff0809170c44f23::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7acb7638ae12c1859ff0809170c44f23::$prefixDirsPsr4;
            $loader->fallbackDirsPsr4 = ComposerStaticInit7acb7638ae12c1859ff0809170c44f23::$fallbackDirsPsr4;
            $loader->classMap = ComposerStaticInit7acb7638ae12c1859ff0809170c44f23::$classMap;

        }, null, ClassLoader::class);
    }
}
