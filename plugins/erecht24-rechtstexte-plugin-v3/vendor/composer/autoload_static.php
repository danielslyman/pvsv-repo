<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitddcd7819b1582b4a1eacc8089dc2abe9
{
    public static $files = array (
        '49a1299791c25c6fd83542c6fedacddd' => __DIR__ . '/..' . '/yahnis-elsts/plugin-update-checker/load-v4p11.php',
    );

    public static $prefixLengthsPsr4 = array (
        'e' => 
        array (
            'eRecht24\\LegalTexts\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'eRecht24\\LegalTexts\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitddcd7819b1582b4a1eacc8089dc2abe9::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitddcd7819b1582b4a1eacc8089dc2abe9::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}