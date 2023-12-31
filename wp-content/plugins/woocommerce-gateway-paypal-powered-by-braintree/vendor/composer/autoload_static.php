<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2c8b15d679b4169ba590cf8ac4f20788
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Braintree\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Braintree\\' => 
        array (
            0 => __DIR__ . '/..' . '/braintree/braintree_php/lib/Braintree',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2c8b15d679b4169ba590cf8ac4f20788::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2c8b15d679b4169ba590cf8ac4f20788::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit2c8b15d679b4169ba590cf8ac4f20788::$classMap;

        }, null, ClassLoader::class);
    }
}
