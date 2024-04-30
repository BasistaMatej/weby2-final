<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit35410904874c84a350154b4d22275205
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Slim\\Http\\' => 10,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Slim\\Http\\' => 
        array (
            0 => __DIR__ . '/..' . '/slim/http/src',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-factory/src',
            1 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit35410904874c84a350154b4d22275205::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit35410904874c84a350154b4d22275205::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit35410904874c84a350154b4d22275205::$classMap;

        }, null, ClassLoader::class);
    }
}