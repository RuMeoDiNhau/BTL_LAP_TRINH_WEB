<?php

declare(strict_types=1);

class UserController extends Controller
{
    private User $users;

    public function __construct(?User $users = null)
    {
        require_admin();
        $this->users = $users ?? new User();
    }

    public function index(): void
    {
        $keyword = trim((string) ($_GET['keyword'] ?? ''));
        $role = (string) ($_GET['role'] ?? '');
        $status = (string) ($_GET['status'] ?? '');
        if (!in_array($role, ['', 'admin', 'member'], true)) {
            $role = '';
        }
        if (!in_array($status, ['', 'active', 'banned'], true)) {
            $status = '';
        }

        $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
        $page = $page === false ? 1 : max(1, $page);
        $result = $this->users->paginateForAdmin($keyword, $role, $status, $page);
        $totalPages = max(1, (int) ceil($result['total'] / 10));
        if ($page > $totalPages) {
            $page = $totalPages;
            $result = $this->users->paginateForAdmin($keyword, $role, $status, $page);
        }

        $this->view('admin/users/index', [
            'pageTitle' => 'Quản lý người dùng | Decor Admin',
            'activeMenu' => 'users',
            'breadcrumb' => 'Quản lý người dùng',
            'users' => $result['users'],
            'total' => $result['total'],
            'page' => $page,
            'totalPages' => $totalPages,
            'filters' => ['keyword' => $keyword, 'role' => $role, 'status' => $status],
        ], 'admin');
    }

    public function show(int $id): void
    {
        $user = $id > 0 ? $this->users->findById($id) : null;
        if ($user === null) {
            http_response_code(404);
            set_flash('error', 'Không tìm thấy tài khoản này.');
            $this->redirect('/admin/users');
        }

        $this->view('admin/users/show', [
            'pageTitle' => 'Thông tin người dùng | Decor Admin',
            'activeMenu' => 'users',
            'breadcrumb' => 'Thông tin người dùng',
            'managedUser' => $user,
            'adminId' => (int) (current_user()['id'] ?? 0),
        ], 'admin');
    }

    public function updateStatus(int $id): void
    {
        if (!$this->validatePost()) {
            $this->redirect('/admin/users/' . $id);
        }
        $status = (string) ($_POST['status'] ?? '');
        if (!in_array($status, ['active', 'banned'], true)) {
            set_flash('error', 'Trạng thái tài khoản không hợp lệ.');
            $this->redirect('/admin/users/' . $id);
        }

        $user = $id > 0 ? $this->users->findById($id) : null;
        if ($user === null) {
            set_flash('error', 'Không tìm thấy tài khoản này.');
        } elseif ($user['role'] !== 'member') {
            set_flash('error', 'Chỉ có thể khóa hoặc mở khóa tài khoản thành viên.');
        } elseif ($user['status'] === $status) {
            set_flash('info', 'Tài khoản đã ở trạng thái này.');
        } elseif ($this->users->updateMemberStatus($id, $status)) {
            set_flash('success', $status === 'banned' ? 'Đã khóa tài khoản người dùng.' : 'Đã mở khóa tài khoản người dùng.');
        } else {
            set_flash('error', 'Không thể cập nhật trạng thái tài khoản.');
        }
        $this->redirect('/admin/users' . ($user === null ? '' : '/' . $id));
    }

    public function resetPassword(int $id): void
    {
        if (!$this->validatePost()) {
            $this->redirect('/admin/users/' . $id);
        }
        $user = $id > 0 ? $this->users->findById($id) : null;
        if ($user === null) {
            set_flash('error', 'Không tìm thấy tài khoản này.');
            $this->redirect('/admin/users');
        }
        if ($user['role'] !== 'member') {
            set_flash('error', 'Không thể đặt lại mật khẩu tài khoản quản trị viên.');
            $this->redirect('/admin/users/' . $id);
        }

        $temporaryPassword = bin2hex(random_bytes(6));
        $hash = password_hash($temporaryPassword, PASSWORD_BCRYPT);
        if (!is_string($hash) || !$this->users->resetMemberPassword($id, $hash)) {
            set_flash('error', 'Không thể đặt lại mật khẩu. Vui lòng thử lại.');
            $this->redirect('/admin/users/' . $id);
        }

        set_flash('success', 'Mật khẩu tạm mới (chỉ hiển thị lần này): ' . $temporaryPassword);
        $this->redirect('/admin/users/' . $id);
    }

    private function validatePost(): bool
    {
        if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST'
            || !verify_csrf(isset($_POST['_csrf']) ? (string) $_POST['_csrf'] : null)) {
            http_response_code(400);
            set_flash('error', 'Phiên gửi biểu mẫu không hợp lệ hoặc đã hết hạn. Vui lòng thử lại.');
            return false;
        }
        return true;
    }
}
