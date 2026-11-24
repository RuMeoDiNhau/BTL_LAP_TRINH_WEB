<?php

declare(strict_types=1);

// Module quản trị người dùng; routes/*.php được public/index.php tự nạp.
$router->get('/admin/users', [UserController::class, 'index']);
$router->get('/admin/users/{id}', [UserController::class, 'show']);
$router->post('/admin/users/{id}/status', [UserController::class, 'updateStatus']);
$router->post('/admin/users/{id}/reset-password', [UserController::class, 'resetPassword']);
