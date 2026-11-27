<?php

declare(strict_types=1);

$router->get('/', [HomeController::class, 'index']);
$router->get('/contact', [PageController::class, 'contact']);
$router->post('/contact', [PageController::class, 'contact']);

$router->get('/admin/settings', [SettingController::class, 'index']);
$router->post('/admin/settings', [SettingController::class, 'update']);
$router->get('/admin/contacts', [ContactAdminController::class, 'index']);
$router->post('/admin/contacts/{id}/status', [ContactAdminController::class, 'updateStatus']);
$router->post('/admin/contacts/{id}/delete', [ContactAdminController::class, 'delete']);
