<?php

declare(strict_types=1);

/** Router dành riêng cho lệnh php -S localhost:8000 -t public public/router.php. */
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
$file = realpath(__DIR__ . $path);
$insidePublic = $file && str_starts_with(str_replace('\\', '/', $file), str_replace('\\', '/', __DIR__) . '/');
$isAsset = preg_match('~^/assets/[A-Za-z0-9_./-]+\.(css|js|svg|png|jpg|jpeg|webp|ico|woff2?)$~D', $path);
$isAvatar = preg_match('~^/uploads/avatars/[a-f0-9]{32}\.(jpg|png|webp)$~D', $path);
if ($insidePublic && is_file($file) && ($isAsset || $isAvatar)) {
    return false;
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
