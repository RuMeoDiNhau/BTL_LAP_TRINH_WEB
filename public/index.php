<?php

declare(strict_types=1);

/**
 * Front controller (PHP >= 7.1 cho core; code của Thiện dùng 7.4/8.0: fn, mixed).
 *
 * THÊM ROUTE CHO MODULE MỚI: tạo file routes/<ten-module>.php rồi dùng biến $router:
 *     $router->get('/about', [PageController::class, 'about']);
 *     $router->post('/faq/ask', [PageController::class, 'ask']);
 * Mọi file routes/*.php được nạp tự động ở cuối file này -> không cần sửa index.php.
 *
 * Chạy local: http://localhost/<ten-thu-muc>/
 * Built-in server: php -S localhost:8000 -t public
 *   (index.php được dùng làm fallback cho URL không phải file tĩnh).
 */

// 1) Session an toàn.
if (session_status() === PHP_SESSION_NONE) {
    $secureSession = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => $secureSession,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    } else {
        session_set_cookie_params(0, '/', '', $secureSession, true);
    }
    session_start();
}

// 2) Config + core + helper.
$configDir = dirname(__DIR__) . '/config';
$configFile = $configDir . '/database.php';
if (!is_file($configFile)) {
    $configFile = $configDir . '/database.example.php';
}
$dbConfig = require $configFile;
if (!is_array($dbConfig)) {
    $dbConfig = require $configDir . '/database.example.php';
}

require_once dirname(__DIR__) . '/core/Database.php';
require_once dirname(__DIR__) . '/core/Router.php';
require_once dirname(__DIR__) . '/core/Helpers.php';
require_once dirname(__DIR__) . '/core/Controller.php';

Database::setConfig($dbConfig);

// BASE_URL không có dấu / cuối (root = ''). Luôn dùng base_url()/asset() để tạo link.
if (!defined('BASE_URL')) {
    define('BASE_URL', rtrim(base_url('/'), '/'));
}

// 3) Autoload đơn giản: class toàn cục trong 3 thư mục (không dùng composer).
spl_autoload_register(static function (string $class): void {
    $short = basename(str_replace('\\', '/', ltrim($class, '\\')));
    $root = dirname(__DIR__);
    $dirs = [
        $root . '/app/Controllers/Admin',
        $root . '/app/Controllers/Client',
        $root . '/app/Models',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . '/' . $short . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

// 4) Đăng ký route cho controller ĐÃ CÓ CODE.
$router = new Router();

// Auth (mục 3.1)
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->post('/logout', [AuthController::class, 'logout']);

// Admin đã có code (mục 3.3)
$router->get('/admin', [DashboardController::class, 'index']);
$router->get('/admin/roles', [DashboardController::class, 'roles']);

// Các module khác để trống; từng người tự khai báo trong routes/*.php.
foreach (glob(dirname(__DIR__) . '/routes/*.php') ?: [] as $routeFile) {
    require $routeFile;
}

$router->dispatch();
