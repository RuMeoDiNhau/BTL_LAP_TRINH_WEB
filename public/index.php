<?php

declare(strict_types=1);

require __DIR__ . '/../core/Helpers.php';
require __DIR__ . '/../core/Auth.php';
require __DIR__ . '/../core/Database.php';
require __DIR__ . '/../core/Controller.php';
require __DIR__ . '/../core/Router.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoload tối giản/ (không Composer).
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

// Admin phần #2.
Router::add('GET', '/admin/faqs', 'App\\Controllers\\Admin\\FaqController@index');
Router::add('GET', '/admin/faqs/create', 'App\\Controllers\\Admin\\FaqController@create');
Router::add('POST', '/admin/faqs/store', 'App\\Controllers\\Admin\\FaqController@store');
Router::add('GET', '/admin/faqs/{id}/edit', 'App\\Controllers\\Admin\\FaqController@edit');
Router::add('POST', '/admin/faqs/{id}/update', 'App\\Controllers\\Admin\\FaqController@update');
Router::add('POST', '/admin/faqs/delete', 'App\\Controllers\\Admin\\FaqController@delete');
Router::add('GET', '/admin/pages/{page}', 'App\\Controllers\\Admin\\PageController@edit');
Router::add('POST', '/admin/pages/{page}/update', 'App\\Controllers\\Admin\\PageController@update');
Router::add('GET', '/admin/faq-questions', 'App\\Controllers\\Admin\\FaqQuestionController@index');
Router::add('GET', '/admin/faq-questions/{id}', 'App\\Controllers\\Admin\\FaqQuestionController@show');
Router::add('POST', '/admin/faq-questions/status', 'App\\Controllers\\Admin\\FaqQuestionController@status');
Router::add('POST', '/admin/faq-questions/delete', 'App\\Controllers\\Admin\\FaqQuestionController@delete');

// TEMP ROUTES - remove this block when the real pages are ready.
Router::add('GET', '/products', 'App\\Controllers\\Client\\PageController@comingSoon');
Router::add('GET', '/pricing', 'App\\Controllers\\Client\\PageController@comingSoon');
Router::add('GET', '/news', 'App\\Controllers\\Client\\PageController@comingSoon');
Router::add('GET', '/contact', 'App\\Controllers\\Client\\PageController@comingSoon');

Router::dispatch();
