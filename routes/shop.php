<?php

declare(strict_types=1);

$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/{slug}', [ProductController::class, 'show']);

$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/items/add', [CartController::class, 'add']);
$router->post('/cart/items/{id}/update', [CartController::class, 'update']);
$router->post('/cart/items/{id}/remove', [CartController::class, 'remove']);
$router->get('/checkout', [CheckoutController::class, 'index']);
$router->post('/checkout', [CheckoutController::class, 'store']);
$router->get('/orders', [OrderController::class, 'index']);
$router->get('/orders/{id}', [OrderController::class, 'show']);

$router->get('/admin/products', [ProductAdminController::class, 'index']);
$router->get('/admin/products/create', [ProductAdminController::class, 'create']);
$router->post('/admin/products/store', [ProductAdminController::class, 'store']);
$router->get('/admin/products/{id}/edit', [ProductAdminController::class, 'edit']);
$router->post('/admin/products/{id}/update', [ProductAdminController::class, 'update']);
$router->post('/admin/products/{id}/delete', [ProductAdminController::class, 'delete']);

$router->get('/admin/carts', [CartAdminController::class, 'index']);
$router->get('/admin/carts/{id}', [CartAdminController::class, 'show']);
$router->post('/admin/carts/{id}/review', [CartAdminController::class, 'review']);
$router->get('/admin/orders', [OrderAdminController::class, 'index']);
$router->get('/admin/orders/{id}', [OrderAdminController::class, 'show']);
$router->post('/admin/orders/{id}/status', [OrderAdminController::class, 'status']);
