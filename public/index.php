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

/*
 * ROUTE TẠM để test hạ tầng trước khi có view.
 * Sẽ thay bằng 'App\Controllers\Client\PageController@about' / '@faq' ở nhóm (a).
 */
$smokePage = static function (string $title, string $description, string $canonical, string $active): callable {
    return static function () use ($title, $description, $canonical, $active): void {
        (new Controller())->render('', [], [
            'title'       => $title,
            'description' => $description,
            'canonical'   => $canonical,
            'og_image'    => 'assets/images/og-default.svg',
            'active'      => $active,
        ]);
    };
};

Router::add('GET', '/about', $smokePage(
    'Giới thiệu | DECOR',
    'Tìm hiểu về DECOR – đơn vị trang trí và nội thất.',
    'about',
    'about'
));
Router::add('GET', '/faq', $smokePage(
    'Hỏi/đáp | DECOR',
    'Giải đáp các câu hỏi thường gặp về sản phẩm và dịch vụ DECOR.',
    'faq',
    'faq'
));

Router::dispatch();
