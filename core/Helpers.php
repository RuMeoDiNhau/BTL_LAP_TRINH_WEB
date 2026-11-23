<?php

/**
 * Hàm tiện ích dùng chung. Được nạp trước Router/Controller trong index.php.
 * BASE_URL tự tính từ SCRIPT_NAME nên chạy được cả dưới Alias /btl/.
 */

/** Trả về '/btl' hoặc '' (normalize '/' thành rỗng). */
function base_url(): string
{
    static $base = null;

    if ($base === null) {
        $script = (string)($_SERVER['SCRIPT_NAME'] ?? '');
        $dir = str_replace('\\', '/', dirname($script));
        $base = ($dir === '/' || $dir === '.' || $dir === '') ? '' : rtrim($dir, '/');
    }

    return $base;
}

/** Link nội bộ: url('about') -> /btl/about. */
function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return base_url() . ($path === '' ? '/' : '/' . $path);
}

/** Asset local: asset('assets/css/x.css') -> /btl/assets/css/x.css. */
function asset(string $path): string
{
    return url($path);
}

/** Icon từ sprite Bootstrap Icons local: <svg class="bi ..."><use href="...#name"></use></svg>. */
function icon(string $name, string $class = ''): string
{
    $name = preg_replace('/[^a-z0-9-]/i', '', $name);
    $class = trim($class);
    $classes = 'bi' . ($class === '' ? '' : ' ' . $class);

    return '<svg class="' . e($classes) . '" aria-hidden="true" width="1em" height="1em" fill="currentColor">'
        . '<use href="' . e(asset('assets/icons/bootstrap-icons.svg') . '#' . $name) . '"></use>'
        . '</svg>';
}

/** URL tuyệt đối cho canonical/OG: scheme + host + BASE_URL + path. */
function absolute_url(string $path = ''): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = (string)($_SERVER['HTTP_HOST'] ?? 'localhost');
    $host = preg_replace('/[^A-Za-z0-9.\-:]/', '', $host) ?: 'localhost';

    return $scheme . '://' . $host . url($path);
}

/** Escape mọi dữ liệu để xuất ra HTML. */
function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Đọc 1 key từ bảng settings, có cache tĩnh và fallback khi CSDL chưa sẵn. */
function setting(string $key, string $default = ''): string
{
    static $cache = [];

    if (!array_key_exists($key, $cache)) {
        try {
            $stmt = Database::pdo()->prepare('SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1');
            $stmt->execute([':key' => $key]);
            $value = $stmt->fetchColumn();
            $cache[$key] = $value === false ? $default : (string)$value;
        } catch (Throwable $error) {
            // Trang public vẫn lên được khi CSDL chưa chạy; phần admin sẽ báo lỗi riêng.
            $cache[$key] = $default;
        }
    }

    return $cache[$key];
}

function csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return (string)$_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(?string $token = null): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $token ??= (string)($_POST['_csrf'] ?? '');

    return $token !== '' && hash_equals((string)($_SESSION['_csrf'] ?? ''), $token);
}

/** Lưu flash message 1 lần vào session. */
function flash_set(string $key, string $message): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['_flash'][$key] = $message;
}

/** Đọc và xóa flash message. */
function flash_get(string $key): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $message = (string)($_SESSION['_flash'][$key] ?? '');
    unset($_SESSION['_flash'][$key]);

    return $message;
}
