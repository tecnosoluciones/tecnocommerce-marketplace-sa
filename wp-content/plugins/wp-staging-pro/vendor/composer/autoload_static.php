<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3d290af12dd0430d8b94ac7430950b78
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WPStaging\\Test\\' => 15,
            'WPStaging\\' => 10,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WPStaging\\Test\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../tests/unit',
        ),
        'WPStaging\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3d290af12dd0430d8b94ac7430950b78::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3d290af12dd0430d8b94ac7430950b78::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
