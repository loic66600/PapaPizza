<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit310e171a99b850dbe769ec40ca2d69f4
{
    public static $files = array (
        '07d7f1a47144818725fd8d91a907ac57' => __DIR__ . '/..' . '/laminas/laminas-diactoros/src/functions/create_uploaded_file.php',
        'da94ac5d3ca7d2dbab84ce561ce72bfd' => __DIR__ . '/..' . '/laminas/laminas-diactoros/src/functions/marshal_headers_from_sapi.php',
        '3d97c8dcdfba8cb85d3b34f116bb248b' => __DIR__ . '/..' . '/laminas/laminas-diactoros/src/functions/marshal_method_from_sapi.php',
        'e6f3bc6883e449ab367280b34158c05b' => __DIR__ . '/..' . '/laminas/laminas-diactoros/src/functions/marshal_protocol_version_from_sapi.php',
        'd59fbae42019aedf227094ac49a46f50' => __DIR__ . '/..' . '/laminas/laminas-diactoros/src/functions/marshal_uri_from_sapi.php',
        'de95e0ac670b27c84ef8c5ac41fc1b34' => __DIR__ . '/..' . '/laminas/laminas-diactoros/src/functions/normalize_server.php',
        'b6c2870932b0250c10334a86dcb33c7f' => __DIR__ . '/..' . '/laminas/laminas-diactoros/src/functions/normalize_uploaded_files.php',
        'd02cf21124526632320d6f20b1bbf905' => __DIR__ . '/..' . '/laminas/laminas-diactoros/src/functions/parse_cookie_header.php',
        'd919fc9d5ad52cfb7f322f7fe36458ab' => __DIR__ . '/..' . '/laminas/laminas-diactoros/src/functions/create_uploaded_file.legacy.php',
        'e397f74f8af3b1e56166a6e99f216ee7' => __DIR__ . '/..' . '/laminas/laminas-diactoros/src/functions/marshal_headers_from_sapi.legacy.php',
        'd154b49fab8e4da34fb553a2d644918c' => __DIR__ . '/..' . '/laminas/laminas-diactoros/src/functions/marshal_method_from_sapi.legacy.php',
        '9d3db23ca418094bcf0b641a0c9559ed' => __DIR__ . '/..' . '/laminas/laminas-diactoros/src/functions/marshal_protocol_version_from_sapi.legacy.php',
        'b0b88a3b89caae681462c58ff19a7059' => __DIR__ . '/..' . '/laminas/laminas-diactoros/src/functions/marshal_uri_from_sapi.legacy.php',
        'cc8e14526dc240491e17a838cb78508c' => __DIR__ . '/..' . '/laminas/laminas-diactoros/src/functions/normalize_server.legacy.php',
        '786bf90caabc9e09b6ad4cc5ca8f0e30' => __DIR__ . '/..' . '/laminas/laminas-diactoros/src/functions/normalize_uploaded_files.legacy.php',
        '751a5a3f463e4be759be31748b61737c' => __DIR__ . '/..' . '/laminas/laminas-diactoros/src/functions/parse_cookie_header.legacy.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stripe\\' => 7,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Container\\' => 14,
        ),
        'M' => 
        array (
            'MiladRahimi\\PhpRouter\\' => 22,
            'MiladRahimi\\PhpContainer\\Tests\\' => 31,
            'MiladRahimi\\PhpContainer\\' => 25,
        ),
        'L' => 
        array (
            'Laminas\\Diactoros\\' => 18,
        ),
        'C' => 
        array (
            'Core\\' => 5,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-factory/src',
            1 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'MiladRahimi\\PhpRouter\\' => 
        array (
            0 => __DIR__ . '/..' . '/miladrahimi/phprouter/src',
        ),
        'MiladRahimi\\PhpContainer\\Tests\\' => 
        array (
            0 => __DIR__ . '/..' . '/miladrahimi/phpcontainer/tests',
        ),
        'MiladRahimi\\PhpContainer\\' => 
        array (
            0 => __DIR__ . '/..' . '/miladrahimi/phpcontainer/src',
        ),
        'Laminas\\Diactoros\\' => 
        array (
            0 => __DIR__ . '/..' . '/laminas/laminas-diactoros/src',
        ),
        'Core\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Core',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/App',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit310e171a99b850dbe769ec40ca2d69f4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit310e171a99b850dbe769ec40ca2d69f4::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit310e171a99b850dbe769ec40ca2d69f4::$classMap;

        }, null, ClassLoader::class);
    }
}
