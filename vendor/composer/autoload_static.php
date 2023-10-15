<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit46f28209d5e56373126bb8c64c803d8f
{
    public static $files = array (
        'c19d4f256a99aba54fc5eea42503ce51' => __DIR__ . '/../..' . '/src/startup.php',
    );

    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'sskaje\\mqtt\\' => 12,
        ),
        'G' => 
        array (
            'G28\\MqttConnection\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'sskaje\\mqtt\\' => 
        array (
            0 => __DIR__ . '/..' . '/sskaje/mqtt/mqtt',
        ),
        'G28\\MqttConnection\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit46f28209d5e56373126bb8c64c803d8f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit46f28209d5e56373126bb8c64c803d8f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit46f28209d5e56373126bb8c64c803d8f::$classMap;

        }, null, ClassLoader::class);
    }
}
