<?php

namespace App\Controllers\Admin;

use App\Models\FaqQuestion;

/** Customer Questions: chỉ đọc/ghi các câu hỏi có source='faq'. */
final class FaqQuestionController extends \Controller
{
    public function index(): void
    {
        \Auth::requireAdmin();

        $perPage = 10;
        $keyword = trim((string)($_GET['q'] ?? ''));
        $status = (string)($_GET['status'] ?? 'all');
        if (!in_array($status, ['all', 'unread', 'read', 'replied'], true)) {
            $status = 'all';
        }
        $filter = $status === 'all' ? null : $status;

        $total = FaqQuestion::count($keyword, $filter);
        $totalPages = max(1, (int)ceil($total / $perPage));
        $page = min(max(1, (int)($_GET['page'] ?? 1)), $totalPages);
        $rows = FaqQuestion::search($keyword, $filter, $perPage, ($page - 1) * $perPage);

        $this->render('admin/faq-questions/index', [
            'rows'       => $rows,
            'keyword'    => $keyword,
            'status'     => $status,
            'page'       => $page,
            'totalPages' => $totalPages,
            'total'      => $total,
            'perPage'    => $perPage,
        ], [
            'title'  => 'Customer Questions',
            'active' => 'admin-faq-questions',
        ], 'admin');
    }

    public function show(string $id): void
    {
        \Auth::requireAdmin();

        $id = (int)$id;
        $row = FaqQuestion::find($id);
        if ($row === []) {
            flash_set('error', 'Question not found.');
            $this->redirect('admin/faq-questions');
            return;
        }

        // Mở trang lần đầu thì chuyển unread -> read; không hạ replied xuống.
        if ($row['status'] === 'unread') {
            FaqQuestion::setStatus($id, 'read');
            $row['status'] = 'read';
        }

        $this->render('admin/faq-questions/show', ['row' => $row], [
            'title'  => 'Customer Questions',
            'active' => 'admin-faq-questions',
        ], 'admin');
    }

    /** POST /admin/faq-questions/status: id + status ('unread'|'read'|'replied'). */
    public function status(): void
    {
        \Auth::requireAdmin();
        if (!csrf_verify()) {
            $this->deny();
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $status = (string)($_POST['status'] ?? '');
        if (!in_array($status, ['unread', 'read', 'replied'], true)) {
            flash_set('error', 'Invalid status.');
            $this->redirect('admin/faq-questions');
            return;
        }
        if ($id <= 0 || FaqQuestion::find($id) === []) {
            flash_set('error', 'Question not found.');
            $this->redirect('admin/faq-questions');
            return;
        }

        FaqQuestion::setStatus($id, $status);
        // Về danh sách để nút "Mark as unread" không bị GET show đổi ngược lại thành read.
        flash_set('success', 'Question status updated.');
        $this->redirect('admin/faq-questions');
    }

    public function delete(): void
    {
        \Auth::requireAdmin();
        if (!csrf_verify()) {
            $this->deny();
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0 && FaqQuestion::find($id) !== []) {
            FaqQuestion::delete($id);
            flash_set('success', 'Question deleted successfully.');
        } else {
            flash_set('error', 'Question not found.');
        }
        $this->redirect('admin/faq-questions');
    }

    private function deny(): void
    {
        http_response_code(403);
        flash_set('error', 'Invalid session. Please try again.');
        $this->redirect('admin/faq-questions');
    }
}
