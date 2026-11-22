<?php

/**
 * Client navigation config. Only items with enabled = true are rendered.
 * 'route' is relative and passed to url(); an empty route means the site root.
 * Routes without a real page yet are served by the temporary coming-soon view.
 */
return [
    'items' => [
        ['label' => 'Home',     'route' => '',         'enabled' => true],
        ['label' => 'About',    'route' => 'about',    'enabled' => true],
        ['label' => 'Products', 'route' => 'products', 'enabled' => true],
        ['label' => 'Pricing',  'route' => 'pricing',  'enabled' => true],
        ['label' => 'News',     'route' => 'news',     'enabled' => true],
        ['label' => 'FAQs',      'route' => 'faq',      'enabled' => true],
        ['label' => 'Contact',  'route' => 'contact',  'enabled' => true],
    ],

    'account' => [
        'guest' => [
            ['label' => 'Login',    'route' => 'login',    'enabled' => false],
            ['label' => 'Register', 'route' => 'register', 'enabled' => false],
        ],
        'user' => [
            ['label' => 'My profile', 'route' => 'profile', 'enabled' => false],
            ['label' => 'Logout',     'route' => 'logout',   'enabled' => false],
        ],
        'admin' => [
            ['label' => 'Admin', 'route' => 'admin', 'enabled' => false],
        ],
    ],

    'cart_enabled' => false,
];
