<?php

namespace App\Controllers\Admin;

use App\Models\Faq;

/** Quản lý FAQ: danh sách, tìm kiếm, lọc trạng thái, phân trang, CRUD. */
final class FaqController extends \Controller
{
    public function index(): void
    {
        \Auth::requireAdmin();

        $perPage = 10;
        $keyword = trim((string)($_GET['q'] ?? ''));
        $status = (string)($_GET['status'] ?? 'all');
        $isActive = $status === '1' ? 1 : ($status === '0' ? 0 : null);
        if (!in_array($status, ['all', '1', '0'], true)) {
            $status = 'all';
        }

        $total = Faq::count($keyword, $isActive);
        $totalPages = max(1, (int)ceil($total / $perPage));
        $page = min(max(1, (int)($_GET['page'] ?? 1)), $totalPages);
        $rows = Faq::search($keyword, $isActive, $perPage, ($page - 1) * $perPage);

        $this->render('admin/faqs/index', [
            'rows'       => $rows,
            'keyword'    => $keyword,
            'status'     => $status,
            'page'       => $page,
            'totalPages' => $totalPages,
            'total'      => $total,
            'perPage'    => $perPage,
        ], [
            'title'  => 'Danh sách FAQ',
            'active' => 'admin-faqs',
        ], 'admin');
    }

    public function create(): void
    {
        \Auth::requireAdmin();
        $this->renderForm([], [], 'Thêm FAQ');
    }

    public function store(): void
    {
        \Auth::requireAdmin();
        if (!csrf_verify()) {
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
        flash_set('success', 'Đã thêm câu hỏi.');
        $this->redirect('admin/faqs');
    }

    public function edit(string $id): void
    {
        \Auth::requireAdmin();
        $row = Faq::find((int)$id);
        if ($row === []) {
            flash_set('error', 'Không tìm thấy câu hỏi.');
            $this->redirect('admin/faqs');
            return;
        }
        $this->renderForm($row, [], 'Sửa FAQ');
    }

    public function update(string $id): void
    {
        \Auth::requireAdmin();
        $id = (int)$id;
        $row = Faq::find($id);
        if ($row === []) {
            flash_set('error', 'Không tìm thấy câu hỏi.');
            $this->redirect('admin/faqs');
            return;
        }
        if (!csrf_verify()) {
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
        flash_set('success', 'Đã cập nhật câu hỏi.');
        $this->redirect('admin/faqs');
    }

    public function delete(): void
    {
        \Auth::requireAdmin();
        if (!csrf_verify()) {
            $this->deny();
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0 && Faq::find($id) !== []) {
            Faq::delete($id);
            flash_set('success', 'Đã xóa câu hỏi.');
        } else {
            flash_set('error', 'Không tìm thấy câu hỏi.');
        }
        $this->redirect('admin/faqs');
    }

    /** @return array<string, mixed> */
    private function input(): array
    {
        return [
            'question'   => trim((string)($_POST['question'] ?? '')),
            'answer'     => trim((string)($_POST['answer'] ?? '')),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
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
        } elseif ($len((string)$data['question']) > 255) {
            $errors['question'] = 'Câu hỏi tối đa 255 ký tự.';
        }
        if ($data['answer'] === '') {
            $errors['answer'] = 'Vui lòng nhập câu trả lời.';
        } elseif ($len((string)$data['answer']) > 5000) {
            $errors['answer'] = 'Câu trả lời tối đa 5000 ký tự.';
        }
        if ((int)$data['sort_order'] < 0) {
            $errors['sort_order'] = 'Thứ tự phải >= 0.';
        }
        return $errors;
    }

    /** @param array<string, mixed> $row @param array<string, string> $errors */
    private function renderForm(array $row, array $errors, string $title): void
    {
        $this->render('admin/faqs/form', [
            'row'    => $row,
            'errors' => $errors,
        ], [
            'title'  => $title,
            'active' => 'admin-faqs',
        ], 'admin');
    }

    private function deny(): void
    {
        http_response_code(403);
        flash_set('error', 'Phiên không hợp lệ, vui lòng thử lại.');
        $this->redirect('admin/faqs');
    }
}
