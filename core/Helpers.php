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

/** Giữ cấu trúc session của nhóm và không đưa password_hash vào dữ liệu giao diện. */
function sync_session_user(array $user): void
{
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'full_name' => (string) $user['full_name'],
        'email' => (string) $user['email'],
        'role' => (string) $user['role'],
        'avatar' => $user['avatar'] ?? null,
    ];
}

function sign_in(array $user): void
{
    session_regenerate_id(true);
    sync_session_user($user);
    $_SESSION['password_version'] = hash('sha256', $user['password_hash']);
    unset($_SESSION['csrf_token']);
}

function sign_out(): void
{
    unset($_SESSION['user'], $_SESSION['password_version'], $_SESSION['csrf_token']);
    session_regenerate_id(true);
}

/** Kiểm tra phiên một lần trước khi xử lý route, kể cả route admin. */
function refresh_session_user(): void
{
    $sessionUser = current_user();
    if ($sessionUser === null) {
        return;
    }

    $user = (new User())->findForAuthenticationById((int) ($sessionUser['id'] ?? 0));
    $version = $_SESSION['password_version'] ?? '';
    if ($user === null || $user['status'] !== 'active'
        || !is_string($version) || !hash_equals($version, hash('sha256', $user['password_hash']))) {
        sign_out();
        return;
    }

    sync_session_user($user);
}

function initials(string $name): string
{
    $words = preg_split('/\s+/u', trim($name), -1, PREG_SPLIT_NO_EMPTY);
    if (!$words) {
        return 'TV';
    }
    $initials = mb_substr($words[0], 0, 1);
    if (count($words) > 1) {
        $initials .= mb_substr(end($words), 0, 1);
    }
    return mb_strtoupper($initials, 'UTF-8');
}

function avatar_url(?string $path): ?string
{
    return $path && preg_match('~^uploads/avatars/[a-f0-9]{32}\.(jpg|png|webp)$~D', $path)
        ? base_url($path) : null;
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
