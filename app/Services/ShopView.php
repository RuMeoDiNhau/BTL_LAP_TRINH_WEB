<?php

declare(strict_types=1);

final class ShopView
{
    public static function storefront(string $activeMenu = 'products'): array
    {
        $head = '';
        foreach (['fonts.css', 'site.css', 'about-faq.css', 'pages/shop.css'] as $stylesheet) {
            $head .= '<link rel="stylesheet" href="' . e(asset('css/' . $stylesheet)) . '">' . "\n";
        }

        return [
            'useShell' => true,
            'activeMenu' => $activeMenu,
            'headExtra' => $head,
            'pageScripts' => ['js/about-faq.js', 'js/pages/shop.js'],
        ];
    }

    public static function admin(string $activeMenu, string $title): array
    {
        return [
            'activeMenu' => $activeMenu,
            'pageTitle' => $title . ' | Decor Admin',
            'breadcrumb' => $title,
            'pageStyles' => ['css/pages/shop.css'],
            'pageScripts' => ['js/pages/shop.js'],
        ];
    }

    public static function money(int $amount): string
    {
        return number_format($amount, 0, ',', '.') . ' ₫';
    }

    public static function image(?string $path): ?string
    {
        if ($path !== null && preg_match('~^(?:uploads/products|assets/images/products|decor-template/decor/images)/[a-zA-Z0-9_-]+\.(?:jpe?g|png|webp)$~iD', $path)) {
            return base_url('/' . $path);
        }
        return null;
    }

    public static function date(string $value): string
    {
        return date('d/m/Y H:i', strtotime($value));
    }
}
