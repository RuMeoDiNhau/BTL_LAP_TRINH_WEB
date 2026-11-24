<?php

declare(strict_types=1);

/**
 * Base controller: xuất view vào layout, redirect, json.
 * Các controller hiện có của Thiện tự render riêng (không kế thừa class này) vẫn chạy bình thường.
 */
abstract class Controller
{
    /** @param array<string,mixed> $data */
    public function view(string $viewPath, array $data = [], ?string $layout = 'layouts/client'): void
    {
        $viewFile = dirname(__DIR__) . '/app/Views/' . ltrim($viewPath, '/') . '.php';
        if (!is_file($viewFile)) {
            throw new RuntimeException('Không tìm thấy view: ' . $viewPath);
        }

        // Đọc flash TRƯỚC, nhưng chỉ khi người gọi không tự truyền (mỗi type đúng 1 lần).
        $sessionFlashSuccess = array_key_exists('flashSuccess', $data) ? null : get_flash('success');
        $sessionFlashError   = array_key_exists('flashError', $data) ? null : get_flash('error');

        // extract trước để $data người gọi luôn thắng, rồi mới điền giá trị mặc định.
        extract($data, EXTR_SKIP);

        $pageTitle    = $pageTitle ?? 'Decor E-Commerce';
        $metaDesc     = $metaDesc ?? 'Decor E-Commerce – Nội thất và giải pháp sống hiện đại.';
        $metaKeywords = $metaKeywords ?? 'Decor, noi that, furniture';
        $activeMenu   = $activeMenu ?? '';
        $breadcrumb   = $breadcrumb ?? '';
        $pageScripts  = $pageScripts ?? [];
        $headExtra    = $headExtra ?? '';
        $currentUser  = $currentUser ?? current_user();
        $csrfToken    = $csrfToken ?? csrf_token(); // layout admin dùng cho form logout
        $flashSuccess = $flashSuccess ?? $sessionFlashSuccess;
        $flashError   = $flashError ?? $sessionFlashError;

        // render_flash() dùng lại giá trị này, không đọc lại $_SESSION.
        $GLOBALS['__decor_flash'] = ['success' => $flashSuccess, 'error' => $flashError];

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        // Cho phép truyền 'client', 'auth' hoặc 'layouts/client'.
        if (strpos($layout, '/') === false) {
            $layout = 'layouts/' . $layout;
        }
        $layoutFile = dirname(__DIR__) . '/app/Views/' . $layout . '.php';
        if (!is_file($layoutFile)) {
            throw new RuntimeException('Không tìm thấy layout: ' . $layout);
        }
        require $layoutFile;
    }

    public function redirect(string $path): void
    {
        header('Location: ' . base_url($path));
        exit;
    }

    /** @param array<string,mixed> $payload */
    public function json(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
