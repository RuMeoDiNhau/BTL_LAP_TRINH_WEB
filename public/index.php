<?php

declare(strict_types=1);

ini_set('display_errors', '0');

define('ROOT_PATH', dirname(__DIR__));
define('BASE_URL', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/.'));

require ROOT_PATH . '/core/Helpers.php';
require ROOT_PATH . '/core/Database.php';
require ROOT_PATH . '/core/Controller.php';
require ROOT_PATH . '/core/Router.php';

spl_autoload_register(static function (string $class): void {
    if (!preg_match('/^[A-Za-z][A-Za-z0-9_]*$/D', $class)) {
        return;
    }
    foreach (['app/Controllers/Client', 'app/Controllers/Admin', 'app/Models', 'app/Services'] as $directory) {
        $file = ROOT_PATH . '/' . $directory . '/' . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

date_default_timezone_set('Asia/Ho_Chi_Minh');
session_name('decor_session');
session_start([
    'use_strict_mode' => true, 'cookie_httponly' => true,
    'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'cookie_samesite' => 'Lax', 'cookie_path' => BASE_URL . '/',
]);
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header('X-Frame-Options: DENY');
header("Content-Security-Policy: default-src 'self'; img-src 'self' blob: data:; style-src 'self'; script-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
header('Cache-Control: no-store');
// Handshake chỉ có trong cấu hình DB thử, tránh chạy smoke test nhầm máy chủ thật.
$testDatabase = getenv('DECOR_DB_DBNAME') ?: '';
if (preg_match('/^decor_account_test[a-z0-9_]*$/D', $testDatabase)) {
    header('X-Decor-Test-Database: ' . $testDatabase);
    header('X-Decor-Test-Post-Max-Size: ' . ini_get('post_max_size'));
}

$router = new Router();
$auth = new AuthController();
$account = new AccountController();
$router->get('/', static function (): void {
    header('Location: ' . url('/account/profile'), true, 302);
});
$router->get('/login', [$auth, 'login']);
$router->post('/login', [$auth, 'authenticate']);
$router->get('/register', [$auth, 'register']);
$router->post('/register', [$auth, 'store']);
$router->post('/logout', [$auth, 'logout']);
$router->get('/account/profile', [$account, 'profile']);
$router->post('/account/profile', [$account, 'updateProfile']);
$router->get('/account/change-password', [$account, 'password']);
$router->post('/account/change-password', [$account, 'updatePassword']);
$router->post('/account/avatar', [$account, 'updateAvatar']);

try {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        throw new RuntimeException('Không thể khởi tạo phiên PHP. Kiểm tra quyền ghi session.save_path.');
    }
    $postLimit = trim(ini_get('post_max_size'));
    $postBytes = (int) ((float) $postLimit * match (strtolower(substr($postLimit, -1))) {
        'g' => 1073741824, 'm' => 1048576, 'k' => 1024, default => 1,
    });
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && $postBytes > 0 && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > $postBytes) {
        http_response_code(413);
        throw new DomainException('Dữ liệu gửi lên quá lớn. Với ảnh đại diện, vui lòng chọn ảnh không vượt quá 2 MB.');
    }
    $route = $_GET['route'] ?? parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (!is_string($route)) {
        throw new DomainException('Đường dẫn không hợp lệ.');
    }
    $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $route);
} catch (DomainException $exception) {
    (new ErrorController())->show($exception->getMessage(), in_array(http_response_code(), [413, 419], true) ? http_response_code() : 400);
} catch (Throwable $exception) {
    error_log(get_class($exception) . ': ' . $exception->getMessage());
    $message = !is_file(ROOT_PATH . '/config/database.php') && getenv('DECOR_DB_HOST') === false
        ? 'Ứng dụng chưa được cấu hình cơ sở dữ liệu. Hãy tạo config/database.php từ file mẫu và import schema.sql, seed.sql theo README.'
        : 'Tạm thời không thể xử lý yêu cầu. Vui lòng thử lại sau hoặc kiểm tra kết nối cơ sở dữ liệu.';
    (new ErrorController())->show($message, 503);
}
