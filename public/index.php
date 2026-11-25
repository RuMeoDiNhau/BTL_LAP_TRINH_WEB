<?php

declare(strict_types=1);

/**
 * Front controller (PHP >= 7.1 cho core; code của Thien dùng 7.4/8.0: fn, mixed).
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
    session_start(['use_strict_mode' => 1]);
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
foreach (['host', 'port', 'dbname', 'username', 'password', 'charset'] as $key) {
    $override = getenv('DECOR_DB_' . strtoupper($key));
    if ($override !== false) {
        $dbConfig[$key] = $override;
    }
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

// 3) Autoload class toàn cục trong app (không dùng Composer).
spl_autoload_register(static function (string $class): void {
    $short = basename(str_replace('\\', '/', ltrim($class, '\\')));
    $root = dirname(__DIR__);
    $dirs = [
        $root . '/app/Controllers/Admin',
        $root . '/app/Controllers/Client',
        $root . '/app/Models',
        $root . '/app/Services',
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

// Auth
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout']);

// Admin )
$router->get('/admin', [DashboardController::class, 'index']);
$router->get('/admin/roles', [DashboardController::class, 'roles']);

// Các module khác để trống; từng người tự khai báo trong routes/*.php.
foreach (glob(dirname(__DIR__) . '/routes/*.php') ?: [] as $routeFile) {
    require $routeFile;
}

// Các module có thể đăng ký trang chủ trước route dự phòng này.
$router->get('/', static function (): void {
    header('Location: ' . base_url(is_logged_in() ? '/account/profile' : '/login'));
});

header('Cache-Control: no-store');
$testDatabase = getenv('DECOR_DB_DBNAME') ?: '';
if (preg_match('/^decor_account_test[a-z0-9_]*$/D', $testDatabase)) {
    header('X-Decor-Test-Database: ' . $testDatabase);
    header('X-Decor-Test-Post-Max-Size: ' . ini_get('post_max_size'));
}

try {
    refresh_session_user();
    $router->dispatch();
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    $path = $router->currentPath();
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $isPageRequest = $method === 'GET' || $method === 'HEAD';
    $message = 'Không thể kết nối cơ sở dữ liệu. Vui lòng thử lại khi MySQL đã sẵn sàng.';

    // Khi phiên cũ chưa xác minh được, vẫn cho phép mở form công khai.
    if ($isPageRequest && $path === '/') {
        header('Location: ' . base_url('/login'));
        return;
    }
    if ($path === '/login' || $path === '/register') {
        if (!$isPageRequest) {
            http_response_code(503);
            set_flash('error', $message);
        }
        $auth = new AuthController();
        if ($path === '/login') {
            $auth->showLogin(false);
        } else {
            $auth->showRegister(false);
        }
        return;
    }
    (new ErrorController())->show($message, 503);
}
