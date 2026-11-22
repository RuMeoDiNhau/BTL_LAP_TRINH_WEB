<?php

/** Base controller: render view vào layout, redirect, json. */
class Controller
{
    /**
     * $view: 'client/about/index' (không .php). Để rỗng khi layout còn đang test.
     * $meta: title, description, canonical (relative), og_image (relative), active.
     */
    public function render(
        string $view = '',
        array $data = [],
        array $meta = [],
        string $layout = 'client'
    ): void {
        $content = '';

        if ($view !== '') {
            $viewFile = dirname(__DIR__) . '/app/Views/' . str_replace('.', '/', $view) . '.php';
            if (is_file($viewFile)) {
                ob_start();
                extract($data, EXTR_SKIP);
                include $viewFile;
                $content = (string)ob_get_clean();
            }
        }

        $title = (string)($meta['title'] ?? 'DECOR');
        $description = (string)($meta['description'] ?? '');
        $canonicalUrl = absolute_url((string)($meta['canonical'] ?? ''));
        $ogImage = absolute_url((string)($meta['og_image'] ?? 'assets/images/og-default.svg'));
        $active = (string)($meta['active'] ?? '');

        $layoutFile = dirname(__DIR__) . '/app/Views/layouts/' . $layout . '.php';
        if (!is_file($layoutFile)) {
            http_response_code(500);
            echo 'Thiếu layout: ' . e($layout);
            return;
        }

        include $layoutFile;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }

    /** @param mixed $payload */
    protected function json($payload): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    }
}
