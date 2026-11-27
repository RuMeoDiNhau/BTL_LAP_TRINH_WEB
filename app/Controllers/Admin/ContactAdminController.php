<?php

declare(strict_types=1);

class ContactAdminController extends Controller
{
    public function __construct()
    {
        require_admin();
    }

    public function index(): void
    {
        $keyword = trim((string) ($_GET['keyword'] ?? ''));
        $status = (string) ($_GET['status'] ?? '');
        if (!in_array($status, ['', 'unread', 'read', 'replied'], true)) {
            $status = '';
        }
        $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
        $page = $page === false ? 1 : max(1, $page);
        $result = (new Contact())->paginate($keyword, $status, $page);

        $this->view('admin/contacts/index', [
            'pageTitle' => 'Liên hệ khách hàng | Decor Admin',
            'activeMenu' => 'contacts',
            'breadcrumb' => 'Liên hệ khách hàng',
            'contacts' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
            'totalPages' => $result['pages'],
            'filters' => ['keyword' => $keyword, 'status' => $status],
        ], 'admin');
    }

    public function updateStatus(int $id): void
    {
        if (!$this->validatePost()) {
            $this->redirect('/admin/contacts');
        }
        $status = (string) ($_POST['status'] ?? '');
        if (!(new Contact())->updateStatus($id, $status)) {
            set_flash('error', 'Không thể cập nhật liên hệ. Vui lòng kiểm tra trạng thái.');
        } else {
            set_flash('success', 'Đã cập nhật trạng thái liên hệ.');
        }
        $this->redirect('/admin/contacts');
    }

    public function delete(int $id): void
    {
        if (!$this->validatePost()) {
            $this->redirect('/admin/contacts');
        }
        if ((new Contact())->delete($id)) {
            set_flash('success', 'Đã xóa liên hệ.');
        } else {
            set_flash('error', 'Không tìm thấy liên hệ cần xóa.');
        }
        $this->redirect('/admin/contacts');
    }

    private function validatePost(): bool
    {
        if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST'
            || !verify_csrf(isset($_POST['_csrf']) ? (string) $_POST['_csrf'] : null)) {
            http_response_code(400);
            set_flash('error', 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.');
            return false;
        }
        return true;
    }
}
