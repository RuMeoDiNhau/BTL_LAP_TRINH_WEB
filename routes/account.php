<?php

declare(strict_types=1);

$router->get('/account/profile', [AccountController::class, 'profile']);
$router->post('/account/profile', [AccountController::class, 'updateProfile']);
$router->get('/account/change-password', [AccountController::class, 'password']);
$router->post('/account/change-password', [AccountController::class, 'updatePassword']);
$router->post('/account/avatar', [AccountController::class, 'updateAvatar']);
