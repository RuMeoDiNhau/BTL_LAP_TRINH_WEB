<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Dùng query route để vẫn chạy được trên Apache chưa bật mod_rewrite. */
function url(string $route = '/'): string
{
    return BASE_URL . '/index.php?route=' . rawurlencode($route);
}

function asset(string $path): string
{
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

function input(string $key, string $default = ''): string
{
    return isset($_POST[$key]) && is_string($_POST[$key]) ? $_POST[$key] : $default;
}

function csrf_token(): string
{
    return $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    if (!hash_equals(csrf_token(), input('_token'))) {
        http_response_code(419);
        throw new DomainException('Phiên biểu mẫu đã hết hạn. Vui lòng tải lại trang và thử lại.');
    }
}

function flash(string $message): void
{
    $_SESSION['flash'] = $message;
}

function take_flash(): ?string
{
    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $message;
}

function sign_in(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['password_version'] = hash('sha256', $user['password_hash']);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function sign_out(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
}

/** Đọc lại trạng thái và hash để tài khoản bị khóa/đổi mật khẩu mất phiên cũ. */
function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $user = (new User())->findById((int) $_SESSION['user_id']);
    if (!$user || $user['status'] !== 'active'
        || !hash_equals($_SESSION['password_version'] ?? '', hash('sha256', $user['password_hash']))) {
        sign_out();
        return null;
    }
    return $user;
}

function initials(string $name): string
{
    $words = preg_split('/\s+/u', trim($name), -1, PREG_SPLIT_NO_EMPTY);
    if (!$words) {
        return 'TV';
    }
    return mb_strtoupper(mb_substr($words[0], 0, 1) . (count($words) > 1 ? mb_substr(end($words), 0, 1) : ''), 'UTF-8');
}

function avatar_url(?string $path): ?string
{
    return $path && preg_match('~^uploads/avatars/[a-f0-9]{32}\.(jpg|png|webp)$~D', $path)
        ? BASE_URL . '/' . $path : null;
}
