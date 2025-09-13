<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for communicating with the core licensing system
    |
    */
    'api_base' => 'https://soft-max.app',
    'api_timeout' => env('SOFTMAX_API_TIMEOUT', 30),
    'product_code' => '684265',

    /*
    |--------------------------------------------------------------------------
    | Installation Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the installation process
    |
    */
    'installation' => [
        'lock_file' => 'installed/installer.lock',
        'encryption_key_file' => 'installed/encryption_key',
        'license_file' => 'installed/license.json',
    ],

    /*
    |--------------------------------------------------------------------------
    | Requirements Configuration
    |--------------------------------------------------------------------------
    |
    | PHP extensions and permissions required for installation
    |
    */
    'requirements' => [
        'php_extensions' => [
            'pdo',
            'pdo_mysql',
            'openssl',
            'mbstring',
            'zip',
            'xml',
            'curl',
            'gd',
            'fileinfo',
            'tokenizer',
            'json',
        ],
        'directories' => [
            'storage',
            'bootstrap/cache',
            'storage/app',
            'storage/framework',
            'storage/logs',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Default database configuration
    |
    */
    'database' => [
        'default_host' => 'localhost',
        'default_port' => '3306',
        'timeout' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Configuration
    |--------------------------------------------------------------------------
    |
    | Default admin user configuration
    |
    */
    'admin' => [
        'default_role' => 'Super Admin',
        'min_password_length' => 8,
    ],

    /*
    |--------------------------------------------------------------------------
    | Installer Routes
    |--------------------------------------------------------------------------
    |
    | Configuration for installer routes
    |
    */
    'routes' => [
        'prefix' => 'softmax-installer',
        'middleware' => ['web'],
    ],
];
