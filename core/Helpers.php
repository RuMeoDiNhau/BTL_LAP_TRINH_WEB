<?php

declare(strict_types=1);

/**
 * Helper dùng chung (QUY_UOC_CHUNG.md mục 2.4).
 * Core chỉ cần PHP >= 7.1. Giữ tên hàm toàn cục để controller/view gọi trực tiếp.
 */

/** Escape HTML chống XSS. */
function e(?string $str): string
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/** Alias ngắn theo AGENTS.md (url()/asset()); nội dung vẫn dùng base_url(). */
function url(string $path = ''): string
{
    return base_url($path);
}

/** Link tuyệt đối theo base path tự tính: '/btl/login', '/assets/...' khi chạy ở docroot. */
function base_url(string $path = ''): string
{
    static $base = null;
    if ($base === null) {
        $base = Router::basePath();
    }

    if (preg_match('#^(https?:)?//#i', $path) === 1) {
        return $path;
    }

    $path = trim($path, '/');
    if ($path === '') {
        return $base === '' ? '/' : $base . '/';
    }
    return $base . '/' . $path;
}

/** Đường dẫn file tĩnh trong public/assets/. */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    if (strpos($path, 'assets/') !== 0) {
        $path = 'assets/' . $path;
    }
    return base_url($path);
}

function csrf_token(): string
{
    $token = $_SESSION['csrf_token'] ?? null;
    if (!is_string($token) || strlen($token) !== 64) {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
    }
    return $token;
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(?string $token): bool
{
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    return is_string($sessionToken)
        && $sessionToken !== ''
        && is_string($token)
        && hash_equals($sessionToken, $token);
}

function set_flash(string $type, string $message): void
{
    if (!in_array($type, ['success', 'error', 'info', 'warning'], true)) {
        $type = 'info';
    }
    $_SESSION['flash'][$type] = $message;
}

/** Đọc rồi xóa ngay (flash chỉ hiện 1 lần). */
function get_flash(string $type): ?string
{
    if (!isset($_SESSION['flash'][$type])) {
        return null;
    }
    $message = (string) $_SESSION['flash'][$type];
    unset($_SESSION['flash'][$type]);
    return $message;
}

/**
 * Trả về HTML flash. Ưu tiên $flashSuccess/$flashError do Controller::view chuẩn bị;
 * nếu không có thì mới đọc get_flash(). Mỗi thông báo chỉ in một lần.
 *
 * @param array<string,mixed>|null $flash Ví dụ: ['success' => $flashSuccess, 'error' => $flashError]
 */
function render_flash(?string $type = null, ?array $flash = null): string
{
    if ($flash === null && isset($GLOBALS['__decor_flash']) && is_array($GLOBALS['__decor_flash'])) {
        $flash = $GLOBALS['__decor_flash'];
    }

    $types = $type === null ? ['success', 'error', 'info', 'warning'] : [$type];
    $html = '';
    foreach ($types as $t) {
        if (is_array($flash) && array_key_exists($t, $flash)) {
            $message = $flash[$t] === null ? null : (string) $flash[$t];
            // Đánh dấu đã in để gọi lại render_flash() không bị lặp.
            unset($flash[$t]);
            if (isset($GLOBALS['__decor_flash']) && is_array($GLOBALS['__decor_flash'])) {
                unset($GLOBALS['__decor_flash'][$t]);
            }
        } else {
            $message = get_flash($t);
        }
        if ($message === null || $message === '') {
            continue;
        }
        $role = $t === 'error' ? 'alert' : 'status';
        $html .= '<div class="flash flash--' . e($t) . '" role="' . $role . '">' . e($message) . '</div>';
    }
    return $html;
}

function current_user(): ?array
{
    $user = $_SESSION['user'] ?? null;
    return is_array($user) ? $user : null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function is_admin(): bool
{
    return ($_SESSION['user']['role'] ?? '') === 'admin';
}

function require_login(): void
{
    if (is_logged_in()) {
        return;
    }
    set_flash('error', 'Vui lòng đăng nhập để tiếp tục.');
    header('Location: ' . base_url('/login'));
    exit;
}

function require_admin(): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Vui lòng đăng nhập để truy cập trang quản trị.');
        header('Location: ' . base_url('/login'));
        exit;
    }
    if (!is_admin()) {
        http_response_code(403);
        $file = dirname(__DIR__) . '/app/Views/errors/403.php';
        if (is_file($file)) {
            require $file;
            exit;
        }
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>403 Forbidden</title></head>'
            . '<body><h1>403 – Không có quyền truy cập</h1></body></html>';
        exit;
    }
}
