<?php

declare(strict_types=1);

class DashboardController extends Controller
{
    private User $userModel;

    public function __construct(?User $userModel = null)
    {
        require_admin();
        $this->userModel = $userModel ?? new User();
    }

    public function index(): void
    {
        $this->view('admin/dashboard', [
            'pageTitle'  => 'Tổng quan Quản trị | Decor Admin',
            'activeMenu' => 'dashboard',
            'breadcrumb' => 'Tổng quan Quản trị',
            'roleCounts' => $this->userModel->countByRole(),
        ], 'layouts/admin');
    }

    public function roles(): void
    {
        $this->view('admin/roles', [
            'pageTitle'   => 'Vai trò & quyền hạn | Decor Admin',
            'activeMenu'  => 'roles',
            'breadcrumb'  => 'Vai trò & quyền hạn',
            'roleCounts'  => $this->userModel->countByRole(),
            'pageScripts' => ['js/admin-roles.js'],
        ], 'layouts/admin');
    }
}
