<?php

declare(strict_types=1);

/** Quản lý FAQ: danh sách, tìm kiếm, lọc, phân trang và CRUD. */
final class FaqController extends Controller
{
    public function index(): void
    {
        require_admin();

        $perPage = 10;
        $keyword = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? 'all');
        if (!in_array($status, ['all', '1', '0'], true)) {
            $status = 'all';
        }
        $isActive = $status === '1' ? 1 : ($status === '0' ? 0 : null);

        $total = Faq::count($keyword, $isActive);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, (int) ($_GET['page'] ?? 1)), $totalPages);
        $rows = Faq::search($keyword, $isActive, $perPage, ($page - 1) * $perPage);

        $this->view('admin/faqs/index', $this->adminData([
            'rows'       => $rows,
            'keyword'    => $keyword,
            'status'     => $status,
            'page'       => $page,
            'totalPages' => $totalPages,
            'total'      => $total,
            'perPage'    => $perPage,
        ], 'Danh sách FAQ', 'admin-faqs', 'js/admin-faqs.js'), 'admin');
    }

    public function create(): void
    {
        require_admin();
        $this->renderForm([], [], 'Thêm FAQ');
    }

    public function store(): void
    {
        require_admin();
        $token = $_POST['_csrf'] ?? null;
        if (!verify_csrf(is_string($token) ? $token : null)) {
            $this->deny();
            return;
        }

        $data = $this->input();
        $errors = $this->validate($data);
        if ($errors !== []) {
            $this->renderForm($data, $errors, 'Thêm FAQ');
            return;
        }

        Faq::create($data);
        set_flash('success', 'Đã thêm câu hỏi.');
        $this->redirect('admin/faqs');
    }

    public function edit($id): void
    {
        require_admin();
        $id = (int) $id;
        $row = Faq::find($id);
        if ($row === []) {
            set_flash('error', 'Không tìm thấy câu hỏi.');
            $this->redirect('admin/faqs');
            return;
        }
        $this->renderForm($row, [], 'Sửa FAQ');
    }

    public function update($id): void
    {
        require_admin();
        $id = (int) $id;
        if (Faq::find($id) === []) {
            set_flash('error', 'Không tìm thấy câu hỏi.');
            $this->redirect('admin/faqs');
            return;
        }
        $token = $_POST['_csrf'] ?? null;
        if (!verify_csrf(is_string($token) ? $token : null)) {
            $this->deny();
            return;
        }

        $data = $this->input();
        $errors = $this->validate($data);
        if ($errors !== []) {
            $data['id'] = $id;
            $this->renderForm($data, $errors, 'Sửa FAQ');
            return;
        }

        Faq::update($id, $data);
        set_flash('success', 'Đã cập nhật câu hỏi.');
        $this->redirect('admin/faqs');
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
        if ($id > 0 && Faq::find($id) !== []) {
            Faq::delete($id);
            set_flash('success', 'Đã xóa câu hỏi.');
        } else {
            set_flash('error', 'Không tìm thấy câu hỏi.');
        }
        $this->redirect('admin/faqs');
    }

    /** @return array<string, mixed> */
    private function input(): array
    {
        return [
            'question'   => trim((string) ($_POST['question'] ?? '')),
            'answer'     => trim((string) ($_POST['answer'] ?? '')),
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'is_active'  => isset($_POST['is_active']) ? 1 : 0,
        ];
    }

    /** @param array<string, mixed> $data @return array<string, string> */
    private function validate(array $data): array
    {
        $errors = [];
        $len = static function (string $v): int {
            return function_exists('mb_strlen') ? mb_strlen($v, 'UTF-8') : strlen($v);
        };
        if ($data['question'] === '') {
            $errors['question'] = 'Vui lòng nhập câu hỏi.';
        } elseif ($len((string) $data['question']) > 255) {
            $errors['question'] = 'Câu hỏi tối đa 255 ký tự.';
        }
        if ($data['answer'] === '') {
            $errors['answer'] = 'Vui lòng nhập câu trả lời.';
        } elseif ($len((string) $data['answer']) > 5000) {
            $errors['answer'] = 'Câu trả lời tối đa 5000 ký tự.';
        }
        if ((int) $data['sort_order'] < 0) {
            $errors['sort_order'] = 'Thứ tự phải >= 0.';
        }
        return $errors;
    }

    /** @param array<string, mixed> $row @param array<string, string> $errors */
    private function renderForm(array $row, array $errors, string $title): void
    {
        $this->view('admin/faqs/form', $this->adminData([
            'row'    => $row,
            'errors' => $errors,
        ], $title, 'admin-faqs', 'js/admin-faqs.js'), 'admin');
    }

    /** @param array<string, mixed> $data @return array<string, mixed> */
    private function adminData(array $data, string $title, string $active, string $script): array
    {
        return array_merge($data, [
            'pageTitle'    => $title,
            'metaDesc'     => 'Decor admin – quản lý câu hỏi thường gặp.',
            'metaKeywords' => 'Decor, FAQ, admin',
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
        $this->redirect('admin/faqs');
    }
}
