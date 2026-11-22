<?php
return [
    'app_name'   => env('APP_NAME', 'CampusCart'),
    'debug'      => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
    'base_url'   => rtrim((string) env('BASE_URL', ''), '/'),
    'timezone'   => 'Asia/Jakarta',
    'upload'     => [
        'max_size' => 2 * 1024 * 1024, // 2 MB
        'allowed'  => ['image/jpeg', 'image/png', 'image/webp'],
    ],
];
