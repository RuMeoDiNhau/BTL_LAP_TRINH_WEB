<?php

declare(strict_types=1);

/** Customer Questions: chỉ đọc/ghi các dòng contacts có source='faq'. */
final class FaqQuestionController extends Controller
{
    public function index(): void
    {
        require_admin();

        $perPage = 10;
        $keyword = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? 'all');
        if (!in_array($status, ['all', 'unread', 'read', 'replied'], true)) {
            $status = 'all';
        }
        $filter = $status === 'all' ? null : $status;

        $total = FaqQuestion::count($keyword, $filter);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, (int) ($_GET['page'] ?? 1)), $totalPages);
        $rows = FaqQuestion::search($keyword, $filter, $perPage, ($page - 1) * $perPage);

        $this->view('admin/faq-questions/index', $this->adminData([
            'rows'       => $rows,
            'keyword'    => $keyword,
            'status'     => $status,
            'page'       => $page,
            'totalPages' => $totalPages,
            'total'      => $total,
            'perPage'    => $perPage,
        ], 'Câu hỏi của khách hàng', 'admin-faq-questions', 'js/admin-faq-questions.js'), 'admin');
    }

    public function show($id): void
    {
        require_admin();

        $id = (int) $id;
        $row = FaqQuestion::find($id);
        if ($row === []) {
            set_flash('error', 'Không tìm thấy câu hỏi.');
            $this->redirect('admin/faq-questions');
            return;
        }

        // Mở lần đầu: unread -> read (không hạ replied xuống).
        if ($row['status'] === 'unread') {
            FaqQuestion::setStatus($id, 'read');
            $row['status'] = 'read';
        }

        $this->view('admin/faq-questions/show', $this->adminData([
            'row' => $row,
        ], 'Câu hỏi khách hàng #' . $id, 'admin-faq-questions', 'js/admin-faq-questions.js'), 'admin');
    }

    /** POST /admin/faq-questions/status */
    public function status(): void
    {
        require_admin();
        $token = $_POST['_csrf'] ?? null;
        if (!verify_csrf(is_string($token) ? $token : null)) {
            $this->deny();
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $status = (string) ($_POST['status'] ?? '');
        if (!in_array($status, ['unread', 'read', 'replied'], true)) {
            set_flash('error', 'Trạng thái không hợp lệ.');
            $this->redirect('admin/faq-questions');
            return;
        }
        if ($id <= 0 || FaqQuestion::find($id) === []) {
            set_flash('error', 'Không tìm thấy câu hỏi.');
            $this->redirect('admin/faq-questions');
            return;
        }

        FaqQuestion::setStatus($id, $status);
        set_flash('success', 'Đã cập nhật trạng thái câu hỏi.');
        // Về danh sách để nút "Đánh dấu chưa đọc" không bị GET show đổi ngược lại.
        $this->redirect('admin/faq-questions');
    }

    public function delete(): void
    {
        require_admin();
        $token = $_POST['_csrf'] ?? null;
        if (!verify_csrf(is_string($token) ? $token : null)) {
            $this->deny();
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0 && FaqQuestion::find($id) !== []) {
            FaqQuestion::delete($id);
            set_flash('success', 'Đã xóa câu hỏi.');
        } else {
            set_flash('error', 'Không tìm thấy câu hỏi.');
        }
        $this->redirect('admin/faq-questions');
    }

    /** @param array<string, mixed> $data @return array<string, mixed> */
    private function adminData(array $data, string $title, string $active, string $script): array
    {
        return array_merge($data, [
            'pageTitle'    => $title,
            'metaDesc'     => 'Decor admin – câu hỏi từ trang FAQ.',
            'metaKeywords' => 'Decor, questions, admin',
            'activeMenu'   => $active,
            'breadcrumb'   => $title,
            'pageStyles'   => ['admin/style.css', 'css/admin-extra.css'],
            'pageScripts'  => ['admin/bundle.js', $script],
            'unreadCount'  => $this->unreadCount(),
        ]);
    }

    private function unreadCount(): int
    {
        try {
            return FaqQuestion::countUnread();
        } catch (PDOException $exception) {
            return 0;
        }
    }

    private function deny(): void
    {
        http_response_code(403);
        set_flash('error', 'Phiên không hợp lệ, vui lòng thử lại.');
        $this->redirect('admin/faq-questions');
    }
}
