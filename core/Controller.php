<?php

declare(strict_types=1);

class Controller
{
    protected function render(string $view, array $data = [], int $status = 200): void
    {
        http_response_code($status);
        extract($data, EXTR_SKIP);
        $viewFile = dirname(__DIR__) . '/app/Views/' . $view . '.php';
        require dirname(__DIR__) . '/app/Views/layouts/client.php';
    }

    protected function redirect(string $route): never
    {
        header('Location: ' . url($route), true, 303);
        exit;
    }

    protected function requireUser(): array
    {
        $user = current_user();
        if (!$user) {
            flash('Vui lòng đăng nhập để quản lý tài khoản của bạn.');
            $this->redirect('/login');
        }
        return $user;
    }
}
