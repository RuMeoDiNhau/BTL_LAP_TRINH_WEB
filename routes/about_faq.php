<?php

declare(strict_types=1);

// ===== #2 About/FAQ =====
// Khối route liền nhau của phần #2. Không sửa thứ tự các file routes/ khác.
$router->get('/about', [AboutFaqController::class, 'about']);
$router->get('/faq', [AboutFaqController::class, 'faq']);
$router->post('/faq/ask', [AboutFaqController::class, 'ask']);

$router->get('/admin/pages/{page}', [PageContentController::class, 'edit']);
$router->post('/admin/pages/{page}/update', [PageContentController::class, 'update']);

$router->get('/admin/faqs', [FaqController::class, 'index']);
$router->get('/admin/faqs/create', [FaqController::class, 'create']);
$router->post('/admin/faqs/store', [FaqController::class, 'store']);
$router->get('/admin/faqs/{id}/edit', [FaqController::class, 'edit']);
$router->post('/admin/faqs/{id}/update', [FaqController::class, 'update']);
$router->post('/admin/faqs/delete', [FaqController::class, 'delete']);

$router->get('/admin/faq-questions', [FaqQuestionController::class, 'index']);
$router->get('/admin/faq-questions/{id}', [FaqQuestionController::class, 'show']);
$router->post('/admin/faq-questions/status', [FaqQuestionController::class, 'status']);
$router->post('/admin/faq-questions/delete', [FaqQuestionController::class, 'delete']);
