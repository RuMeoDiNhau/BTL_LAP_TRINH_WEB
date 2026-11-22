<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/Models/User.php';

class DashboardController
{
    private User $userModel;

    public function __construct(?User $userModel = null)
    {
        $this->ensureSession();
        $this->ensureAdmin();
        $this->userModel = $userModel ?? new User();
    }

    public function index(): void
    {
        $roleCounts = $this->userModel->countByRole();

        $this->renderAdmin('admin/dashboard', [
            'pageTitle'    => 'Tổng quan Quản trị | Decor Admin',
            'activeMenu'   => 'dashboard',
            'breadcrumb'   => 'Tổng quan Quản trị',
            'roleCounts'   => $roleCounts,
            'currentUser'  => $_SESSION['user'],
            'csrfToken'    => $this->getCsrfToken(),
            'flashSuccess' => $this->pullFlash('success'),
            'flashError'   => $this->pullFlash('error'),
        ]);
    }

    public function roles(): void
    {
        $roleCounts = $this->userModel->countByRole();

        $this->renderAdmin('admin/roles', [
            'pageTitle'    => 'Vai trò & quyền hạn | Decor Admin',
            'activeMenu'   => 'roles',
            'breadcrumb'   => 'Vai trò & quyền hạn',
            'roleCounts'   => $roleCounts,
            'currentUser'  => $_SESSION['user'],
            'csrfToken'    => $this->getCsrfToken(),
            'flashSuccess' => $this->pullFlash('success'),
            'flashError'   => $this->pullFlash('error'),
            'pageScripts'  => ['js/admin-roles.js'],
        ]);
    }

    private function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function ensureAdmin(): void
    {
        if (function_exists('require_admin')) {
            require_admin();
            return;
        }

        if (empty($_SESSION['user'])) {
            $_SESSION['flash']['error'] = 'Vui lòng đăng nhập để truy cập trang quản trị.';
            $url = function_exists('base_url') ? base_url('/login') : '/login';
            header('Location: ' . $url);
            exit;
        }

        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            http_response_code(403);
            $_SESSION['flash']['error'] = 'Bạn không có quyền truy cập khu vực Quản trị viên.';
            echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>403 Forbidden</title></head>'
                . '<body style="font-family:Inter,sans-serif;padding:48px;text-align:center;">'
                . '<h1>403 – Không có quyền truy cập</h1>'
                . '<p>Tài khoản Thành viên (member) không được phép truy cập khu vực Quản trị viên.</p>'
                . '<p><a href="' . htmlspecialchars(function_exists('base_url') ? base_url('/') : '/', ENT_QUOTES, 'UTF-8') . '">Quay về trang chủ</a></p>'
                . '</body></html>';
            exit;
        }
    }

    private function getCsrfToken(): string
    {
        if (function_exists('csrf_token')) {
            return (string) csrf_token();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return (string) $_SESSION['csrf_token'];
    }

    private function pullFlash(string $type): ?string
    {
        if (function_exists('get_flash')) {
            $val = get_flash($type);
            return is_string($val) ? $val : null;
        }
        if (!isset($_SESSION['flash'][$type])) {
            return null;
        }
        $message = (string) $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }

    private function renderAdmin(string $viewPath, array $data = []): void
    {
        $viewsDir = dirname(__DIR__, 2) . '/Views';
        $viewFile = $viewsDir . '/' . ltrim($viewPath, '/') . '.php';
        $layoutFile = $viewsDir . '/layouts/admin.php';

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require $layoutFile;
    }
}
