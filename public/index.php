<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

require APP_PATH . '/core/helpers.php';
load_env(ROOT_PATH . '/.env');

date_default_timezone_set(config('config.timezone', 'Asia/Jakarta'));

// Autoloader: kelas global dicari di folder inti, kelas ber-namespace App\... mengikuti folder (huruf kecil)
spl_autoload_register(function (string $class): void {
    if (str_starts_with($class, 'App\\')) {
        $parts = explode('\\', substr($class, 4));
        $file  = array_pop($parts);
        $dir   = strtolower(implode('/', $parts));
        $path  = APP_PATH . '/' . ($dir !== '' ? $dir . '/' : '') . $file . '.php';
        if (is_file($path)) {
            require $path;
        }
        return;
    }
    foreach (['core', 'controllers', 'models', 'middleware', 'services'] as $dir) {
        $path = APP_PATH . "/{$dir}/{$class}.php";
        if (is_file($path)) {
            require $path;
            return;
        }
    }
});

// Error handling
ini_set('display_errors', config('config.debug') ? '1' : '0');
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', ROOT_PATH . '/storage/logs/error.log');

set_exception_handler(function (Throwable $e): void {
    error_log((string) $e);
    if (config('config.debug')) {
        http_response_code(500);
        echo '<pre>' . e($e->getMessage() . "\n" . $e->getTraceAsString()) . '</pre>';
    } else {
        abort(500);
    }
});

// Session
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => !empty($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// Routing
$router = new Router();
require ROOT_PATH . '/routes/web.php';
$router->dispatch();