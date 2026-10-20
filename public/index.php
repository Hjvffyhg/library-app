<?php

declare(strict_types=1);

use App\Core\Router;
use Dotenv\Dotenv;

$projectRoot = dirname(__DIR__);
$autoloadFile = $projectRoot . '/vendor/autoload.php';

if (!is_file($autoloadFile)) {
    http_response_code(503);
    echo 'Composer dependencies are missing. Open a terminal in the project folder and run: composer install';
    exit;
}

require $autoloadFile;
Dotenv::createImmutable($projectRoot)->safeLoad();
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Asia/Manila');

$isHttps = (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
    || (string) ($_SERVER['SERVER_PORT'] ?? '') === '443';
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');
session_name('library_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self'; script-src 'self'; form-action 'self'; frame-ancestors 'self'; base-uri 'self'");

$router = new Router();
require $projectRoot . '/routes/web.php';

try {
    $router->dispatch();
} catch (Throwable $exception) {
    error_log((string) $exception);
    http_response_code(500);

    $debug = filter_var($_ENV['APP_DEBUG'] ?? 'false', FILTER_VALIDATE_BOOL);
    if ($debug && ($_ENV['APP_ENV'] ?? 'local') !== 'production') {
        echo '<h1>Application error</h1><pre>' . htmlspecialchars((string) $exception, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>';
    } else {
        echo '<h1>Something went wrong</h1><p>The error has been logged. Please try again later.</p>';
    }
}
