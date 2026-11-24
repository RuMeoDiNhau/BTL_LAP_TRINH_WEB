<?php

declare(strict_types=1);

$router->get('/posts', [PostController::class, 'index']);
$router->get('/posts/{slug}', [PostController::class, 'show']);
$router->post('/posts/{slug}/comments', [PostController::class, 'storeComment']);

$router->get('/admin/posts', [PostAdminController::class, 'index']);
$router->get('/admin/posts/create', [PostAdminController::class, 'create']);
$router->post('/admin/posts/store', [PostAdminController::class, 'store']);
$router->get('/admin/posts/{id}/edit', [PostAdminController::class, 'edit']);
$router->post('/admin/posts/{id}/update', [PostAdminController::class, 'update']);
$router->post('/admin/posts/{id}/delete', [PostAdminController::class, 'delete']);
$router->post('/admin/posts/upload-image', [PostAdminController::class, 'uploadImage']);

$router->get('/admin/comments', [PostAdminController::class, 'comments']);
$router->post('/admin/comments/{id}/status', [PostAdminController::class, 'commentStatus']);
$router->post('/admin/comments/{id}/delete', [PostAdminController::class, 'commentDelete']);
$router->post('/admin/comments/moderation', [PostAdminController::class, 'toggleModeration']);
