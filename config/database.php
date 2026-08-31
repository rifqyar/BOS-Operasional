<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    */

    'default' => env('DB_CONNECTION', 'oracle'),


    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    */

    'connections' => [


        /*
        |--------------------------------------------------------------------------
        | Oracle Main Connection
        |--------------------------------------------------------------------------
        */

        'oracle' => [

            'driver' => 'oracle',

            'tns' => env('DB_TNS'),

            'host' => env('DB_HOST', '127.0.0.1'),

            'port' => env('DB_PORT', '1521'),

            'database' => env('DB_DATABASE', 'freepdb1'),

            'service_name' => env(
                'DB_SERVICE_NAME',
                'freepdb1'
            ),

            'username' => env(
                'DB_USERNAME',
                'TPK_IPC'
            ),

            'password' => env(
                'DB_PASSWORD',
                ''
            ),

            'charset' => env(
                'DB_CHARSET',
                'AL32UTF8'
            ),

            'prefix' => env(
                'DB_PREFIX',
                ''
            ),

            'prefix_schema' => env(
                'DB_SCHEMA_PREFIX',
                ''
            ),

            'edition' => env(
                'DB_EDITION',
                'ora$base'
            ),

            'server_version' => env(
                'DB_SERVER_VERSION',
                '23'
            ),

            'load_balance' => false,

            'dynamic' => [],

        ],



        /*
        |--------------------------------------------------------------------------
        | Legacy Production Connection
        |--------------------------------------------------------------------------
        |
        | Dipakai oleh source lama:
        | DB::connection('prod')
        |
        */

        'prod' => [

            'driver' => 'oracle',

            'tns' => env('DB_TNS'),

            'host' => env('DB_HOST'),

            'port' => env('DB_PORT'),

            'database' => env('DB_DATABASE'),

            'service_name' => env('DB_SERVICE_NAME'),

            'username' => env('DB_USERNAME'),

            'password' => env('DB_PASSWORD'),

            'charset' => 'AL32UTF8',

        ],



        /*
        |--------------------------------------------------------------------------
        | SQLite
        |--------------------------------------------------------------------------
        */

        'sqlite' => [

            'driver' => 'sqlite',

            'url' => env('DB_URL'),

            'database' => env(
                'DB_DATABASE',
                database_path('database.sqlite')
            ),

            'prefix' => '',

            'foreign_key_constraints' => true,

        ],



        /*
        |--------------------------------------------------------------------------
        | MySQL
        |--------------------------------------------------------------------------
        |
        | Tidak digunakan karena sekarang Oracle
        |
        */

        'mysql' => [

            'driver' => 'mysql',

            'url' => env('DB_URL'),

            'host' => env(
                'DB_HOST',
                '127.0.0.1'
            ),

            'port' => env(
                'DB_PORT',
                '3306'
            ),

            'database' => env(
                'DB_DATABASE',
                'laravel'
            ),

            'username' => env(
                'DB_USERNAME',
                'root'
            ),

            'password' => env(
                'DB_PASSWORD',
                ''
            ),

            'unix_socket' => env(
                'DB_SOCKET',
                ''
            ),

            'charset' => 'utf8mb4',

            'collation' => 'utf8mb4_unicode_ci',

            'prefix' => '',

            'prefix_indexes' => true,

            'strict' => true,

            'engine' => null,


            /*
            |--------------------------------------------------------------------------
            | PHP 8.5 Deprecated
            |--------------------------------------------------------------------------
            |
            | PDO::MYSQL_ATTR_SSL_CA deprecated
            |
            */

            /*
            'options' => extension_loaded('pdo_mysql')
                ? array_filter([
                    Pdo\Mysql::ATTR_SSL_CA =>
                        env('MYSQL_ATTR_SSL_CA'),
                ])
                : [],
            */

        ],



        /*
        |--------------------------------------------------------------------------
        | PostgreSQL
        |--------------------------------------------------------------------------
        |
        | Tidak digunakan
        |
        */

        /*
        'pgsql' => [

            'driver' => 'pgsql',

            'url' => env('DB_URL'),

            'host' => env(
                'DB_HOST',
                '127.0.0.1'
            ),

            'port' => env(
                'DB_PORT',
                '5432'
            ),

            'database' => env(
                'DB_DATABASE',
                'laravel'
            ),

            'username' => env(
                'DB_USERNAME',
                'root'
            ),

            'password' => env(
                'DB_PASSWORD',
                ''
            ),

            'charset' => 'utf8',

            'prefix' => '',

            'prefix_indexes' => true,

            'search_path' => 'public',

            'sslmode' => 'prefer',

        ],
        */



        /*
        |--------------------------------------------------------------------------
        | MariaDB
        |--------------------------------------------------------------------------
        */

        'mariadb' => [

            'driver' => 'mariadb',

            'url' => env('DB_URL'),

            'host' => env(
                'DB_HOST',
                '127.0.0.1'
            ),

            'port' => env(
                'DB_PORT',
                '3306'
            ),

            'database' => env(
                'DB_DATABASE',
                'laravel'
            ),

            'username' => env(
                'DB_USERNAME',
                'root'
            ),

            'password' => env(
                'DB_PASSWORD',
                ''
            ),

            'charset' => 'utf8mb4',

            'collation' => 'utf8mb4_unicode_ci',

            'prefix' => '',

            'prefix_indexes' => true,

            'strict' => true,


            /*
            'options' => extension_loaded('pdo_mysql')
                ? array_filter([
                    Pdo\Mysql::ATTR_SSL_CA =>
                        env('MYSQL_ATTR_SSL_CA'),
                ])
                : [],
            */

        ],



        /*
        |--------------------------------------------------------------------------
        | SQL Server
        |--------------------------------------------------------------------------
        */

        'sqlsrv' => [

            'driver' => 'sqlsrv',

            'url' => env('DB_URL'),

            'host' => env(
                'DB_HOST',
                'localhost'
            ),

            'port' => env(
                'DB_PORT',
                '1433'
            ),

            'database' => env(
                'DB_DATABASE',
                'laravel'
            ),

            'username' => env(
                'DB_USERNAME',
                'root'
            ),

            'password' => env(
                'DB_PASSWORD',
                ''
            ),

            'charset' => 'utf8',

            'prefix' => '',

            'prefix_indexes' => true,

        ],


    ],



    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    */

    'migrations' => [

        'table' => 'migrations',

        'update_date_on_publish' => true,

    ],



    /*
    |--------------------------------------------------------------------------
    | Redis
    |--------------------------------------------------------------------------
    */

    'redis' => [

        'client' => env(
            'REDIS_CLIENT',
            'phpredis'
        ),

        'options' => [

            'cluster' => env(
                'REDIS_CLUSTER',
                'redis'
            ),

            'prefix' => env(
                'REDIS_PREFIX',
                Str::slug(
                    env('APP_NAME','laravel'),
                    '_'
                ).'_database_'
            ),

        ],


        'default' => [

            'url' => env('REDIS_URL'),

            'host' => env(
                'REDIS_HOST',
                '127.0.0.1'
            ),

            'username' => env('REDIS_USERNAME'),

            'password' => env('REDIS_PASSWORD'),

            'port' => env(
                'REDIS_PORT',
                '6379'
            ),

            'database' => env(
                'REDIS_DB',
                '0'
            ),

        ],

    ],

];