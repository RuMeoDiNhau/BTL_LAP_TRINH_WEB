<?php

declare(strict_types=1);

require __DIR__ . '/../core/Helpers.php';
require __DIR__ . '/../core/Database.php';
require __DIR__ . '/../core/Controller.php';
require __DIR__ . '/../core/Router.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoload tối giản cho app/ (không Composer).
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $file = dirname(__DIR__) . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

Router::add('GET', '/', 'App\\Controllers\\Client\\PageController@home');
Router::add('GET', '/about', 'App\\Controllers\\Client\\PageController@about');
Router::add('GET', '/faq', 'App\\Controllers\\Client\\PageController@faq');
Router::add('POST', '/faq/ask', 'App\\Controllers\\Client\\PageController@ask');

// TEMP ROUTES - remove this block when the real pages are ready.
Router::add('GET', '/products', 'App\\Controllers\\Client\\PageController@comingSoon');
Router::add('GET', '/pricing', 'App\\Controllers\\Client\\PageController@comingSoon');
Router::add('GET', '/news', 'App\\Controllers\\Client\\PageController@comingSoon');
Router::add('GET', '/contact', 'App\\Controllers\\Client\\PageController@comingSoon');

Router::dispatch();
