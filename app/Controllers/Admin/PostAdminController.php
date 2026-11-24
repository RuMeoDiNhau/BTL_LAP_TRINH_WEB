<?php

declare(strict_types=1);

class PostAdminController extends Controller
{
    private Post $postModel;
    private Comment $commentModel;

    public function __construct(?Post $postModel = null, ?Comment $commentModel = null)
    {
        require_admin();
        $this->postModel = $postModel ?? new Post();
        $this->commentModel = $commentModel ?? new Comment();
    }

    public function index(): void
    {
        $keyword = trim((string) ($_GET['keyword'] ?? ''));
        $statusParam = (string) ($_GET['status'] ?? '');
        $status = in_array($statusParam, ['draft', 'published'], true) ? $statusParam : null;

        $perPage = 10;
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $totalFiltered = $this->postModel->adminCount($keyword, $status);
        $totalPages = max(1, (int) ceil($totalFiltered / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        $posts = $this->postModel->adminPaginate($keyword, $status, $perPage, $offset);

        $this->view('admin/posts/index', [
            'pageTitle'      => 'Quản lý Tin tức / Bài viết | Decor Admin',
            'activeMenu'     => 'posts',
            'breadcrumb'     => 'Bài viết / Tin tức',
            'posts'          => $posts,
            'keyword'        => $keyword,
            'status'         => $status ?? '',
            'page'           => $page,
            'totalPages'     => $totalPages,
            'totalFiltered'  => $totalFiltered,
            'totalAll'       => $this->postModel->adminCount('', null),
            'totalPublished' => $this->postModel->adminCount('', 'published'),
            'totalDraft'     => $this->postModel->adminCount('', 'draft'),
            'pageScripts'    => ['js/admin-posts.js'],
        ], 'layouts/admin');
    }

    public function create(): void
    {
        $this->view('admin/posts/form', [
            'pageTitle'   => 'Thêm bài viết mới | Decor Admin',
            'activeMenu'  => 'posts',
            'breadcrumb'  => 'Thêm bài viết mới',
            'isEdit'      => false,
            'post'        => [
                'id'               => 0,
                'title'            => '',
                'slug'             => '',
                'summary'          => '',
                'content'          => '',
                'thumbnail'        => '',
                'meta_title'       => '',
                'meta_description' => '',
                'meta_keywords'    => '',
                'status'           => 'published',
            ],
            'errors'      => [],
            'pageScripts' => ['js/admin-posts.js'],
        ], 'layouts/admin');
    }

    public function store(): void
    {
        if (!verify_csrf($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Phiên biểu mẫu không hợp lệ (CSRF).');
            $this->redirect('/admin/posts/create');
        }

        [$data, $errors] = $this->validatePostInput($_POST, null);

        $uploadedThumbnail = null;
        if (isset($_FILES['thumbnail']) && ($_FILES['thumbnail']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            [$path, $uploadErr] = $this->handleImageUpload($_FILES['thumbnail']);
            if ($uploadErr !== null) {
                $errors['thumbnail'] = $uploadErr;
            } else {
                $uploadedThumbnail = $path;
            }
        }

        if ($errors !== []) {
            if ($uploadedThumbnail !== null) {
                $this->removeUploadedFile($uploadedThumbnail);
            }
            $data['thumbnail'] = '';
            $this->view('admin/posts/form', [
                'pageTitle'   => 'Thêm bài viết mới | Decor Admin',
                'activeMenu'  => 'posts',
                'breadcrumb'  => 'Thêm bài viết mới',
                'isEdit'      => false,
                'post'        => $data,
                'errors'      => $errors,
                'flashError'  => 'Chưa thể lưu bài viết. Vui lòng kiểm tra lại các trường bị lỗi.',
                'pageScripts' => ['js/admin-posts.js'],
            ], 'layouts/admin');
            return;
        }

        $user = current_user();
        $data['author_id'] = (int) ($user['id'] ?? 1);
        $data['thumbnail'] = $uploadedThumbnail;

        $this->postModel->create($data);

        set_flash('success', 'Đã thêm bài viết mới thành công!');
        $this->redirect('/admin/posts');
    }

    public function edit(int $id): void
    {
        $post = $this->postModel->findById($id);
        if ($post === null) {
            set_flash('error', 'Không tìm thấy bài viết cần chỉnh sửa.');
            $this->redirect('/admin/posts');
        }

        $this->view('admin/posts/form', [
            'pageTitle'   => 'Chỉnh sửa bài viết | Decor Admin',
            'activeMenu'  => 'posts',
            'breadcrumb'  => 'Chỉnh sửa bài viết',
            'isEdit'      => true,
            'post'        => $post,
            'errors'      => [],
            'pageScripts' => ['js/admin-posts.js'],
        ], 'layouts/admin');
    }

    public function update(int $id): void
    {
        if (!verify_csrf($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Phiên biểu mẫu không hợp lệ (CSRF).');
            $this->redirect('/admin/posts/' . $id . '/edit');
        }

        $existing = $this->postModel->findById($id);
        if ($existing === null) {
            set_flash('error', 'Bài viết không tồn tại.');
            $this->redirect('/admin/posts');
        }

        [$data, $errors] = $this->validatePostInput($_POST, $id);

        $newThumbnail = null;
        if (isset($_FILES['thumbnail']) && ($_FILES['thumbnail']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            [$path, $uploadErr] = $this->handleImageUpload($_FILES['thumbnail']);
            if ($uploadErr !== null) {
                $errors['thumbnail'] = $uploadErr;
            } else {
                $newThumbnail = $path;
            }
        }

        if ($errors !== []) {
            if ($newThumbnail !== null) {
                $this->removeUploadedFile($newThumbnail);
            }
            $data['id'] = $id;
            $data['thumbnail'] = (string) ($existing['thumbnail'] ?? '');
            $this->view('admin/posts/form', [
                'pageTitle'   => 'Chỉnh sửa bài viết | Decor Admin',
                'activeMenu'  => 'posts',
                'breadcrumb'  => 'Chỉnh sửa bài viết',
                'isEdit'      => true,
                'post'        => $data,
                'errors'      => $errors,
                'flashError'  => 'Cập nhật thất bại. Vui lòng kiểm tra lại các trường bị lỗi.',
                'pageScripts' => ['js/admin-posts.js'],
            ], 'layouts/admin');
            return;
        }

        $data['thumbnail'] = $newThumbnail ?? ($existing['thumbnail'] ?? null);
        $this->postModel->update($id, $data);

        if ($newThumbnail !== null && !empty($existing['thumbnail'])) {
            $this->removeUploadedFile((string) $existing['thumbnail']);
        }

        set_flash('success', 'Đã cập nhật bài viết thành công!');
        $this->redirect('/admin/posts');
    }

    public function delete(int $id): void
    {
        if (!verify_csrf($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Phiên biểu mẫu không hợp lệ (CSRF).');
            $this->redirect('/admin/posts');
        }

        $existing = $this->postModel->findById($id);
        if ($existing === null) {
            set_flash('error', 'Bài viết không tồn tại.');
            $this->redirect('/admin/posts');
        }

        $this->postModel->delete($id);
        if (!empty($existing['thumbnail'])) {
            $this->removeUploadedFile((string) $existing['thumbnail']);
        }

        set_flash('success', 'Đã xóa bài viết thành công.');
        $this->redirect('/admin/posts');
    }

    public function uploadImage(): void
    {
        $csrf = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
        if (!verify_csrf(is_string($csrf) ? $csrf : null)) {
            $this->json(['error' => 'CSRF token không hợp lệ.'], 403);
        }

        $file = $_FILES['file'] ?? null;
        if (!is_array($file)) {
            $this->json(['error' => 'Không nhận được file ảnh.'], 400);
        }

        [$path, $err] = $this->handleImageUpload($file);
        if ($err !== null || $path === null) {
            $this->json(['error' => $err ?? 'Upload thất bại.'], 400);
        }

        $this->json(['location' => base_url('/' . ltrim($path, '/'))], 200);
    }

    public function comments(): void
    {
        $keyword = trim((string) ($_GET['keyword'] ?? ''));
        $statusParam = (string) ($_GET['status'] ?? '');
        $status = in_array($statusParam, ['pending', 'approved', 'hidden'], true) ? $statusParam : null;
        $postIdParam = (int) ($_GET['post_id'] ?? 0);
        $postId = $postIdParam > 0 ? $postIdParam : null;

        $perPage = 10;
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $totalFiltered = $this->commentModel->adminCount($status, $postId, $keyword);
        $totalPages = max(1, (int) ceil($totalFiltered / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        $comments = $this->commentModel->adminPaginate($status, $postId, $keyword, $perPage, $offset);
        $allPosts = $this->postModel->adminPaginate('', null, 100, 0);

        $this->view('admin/comments/index', [
            'pageTitle'      => 'Quản lý Bình luận | Decor Admin',
            'activeMenu'     => 'comments',
            'breadcrumb'     => 'Bình luận bài viết',
            'comments'       => $comments,
            'allPosts'       => $allPosts,
            'keyword'        => $keyword,
            'status'         => $status ?? '',
            'postId'         => $postId ?? 0,
            'page'           => $page,
            'totalPages'     => $totalPages,
            'totalFiltered'  => $totalFiltered,
            'totalAll'       => $this->commentModel->adminCount(null, null, ''),
            'totalApproved'  => $this->commentModel->adminCount('approved', null, ''),
            'totalPending'   => $this->commentModel->adminCount('pending', null, ''),
            'totalHidden'    => $this->commentModel->adminCount('hidden', null, ''),
            'moderationOn'   => $this->commentModel->isModerationEnabled(),
            'pageScripts'    => ['js/admin-comments.js'],
        ], 'layouts/admin');
    }

    public function commentStatus(int $id): void
    {
        if (!verify_csrf($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Phiên biểu mẫu không hợp lệ (CSRF).');
            $this->redirect('/admin/comments');
        }

        $status = (string) ($_POST['status'] ?? '');
        if (!$this->commentModel->updateStatus($id, $status)) {
            set_flash('error', 'Trạng thái bình luận không hợp lệ.');
            $this->redirect('/admin/comments');
        }

        set_flash('success', 'Đã cập nhật trạng thái bình luận thành công.');
        $this->redirect('/admin/comments');
    }

    public function commentDelete(int $id): void
    {
        if (!verify_csrf($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Phiên biểu mẫu không hợp lệ (CSRF).');
            $this->redirect('/admin/comments');
        }

        $this->commentModel->delete($id);
        set_flash('success', 'Đã xóa bình luận.');
        $this->redirect('/admin/comments');
    }

    public function toggleModeration(): void
    {
        if (!verify_csrf($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Phiên biểu mẫu không hợp lệ (CSRF).');
            $this->redirect('/admin/comments');
        }

        $enabled = ($_POST['enabled'] ?? '0') === '1';
        $this->commentModel->setModerationEnabled($enabled);
        set_flash(
            'success',
            $enabled ? 'Đã bật chế độ kiểm duyệt bình luận trước khi hiển thị.' : 'Đã tắt chế độ kiểm duyệt bình luận.'
        );
        $this->redirect('/admin/comments');
    }

    private function validatePostInput(array $input, ?int $ignoreId): array
    {
        $title = trim((string) ($input['title'] ?? ''));
        $rawSlug = trim((string) ($input['slug'] ?? ''));
        $slug = $rawSlug !== '' ? $rawSlug : Post::generateSlug($title);
        $summary = trim((string) ($input['summary'] ?? ''));
        $content = $this->sanitizeHtml((string) ($input['content'] ?? ''));
        $metaTitle = trim((string) ($input['meta_title'] ?? ''));
        $metaDesc = trim((string) ($input['meta_description'] ?? ''));
        $metaKeywords = trim((string) ($input['meta_keywords'] ?? ''));
        $status = in_array($input['status'] ?? 'draft', ['draft', 'published'], true)
            ? (string) $input['status']
            : 'draft';

        $errors = [];

        $titleLen = mb_strlen($title, 'UTF-8');
        if ($titleLen < 5 || $titleLen > 255) {
            $errors['title'] = 'Tiêu đề bài viết phải từ 5 đến 255 ký tự.';
        }

        if ($slug === '' || mb_strlen($slug, 'UTF-8') > 280 || preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) !== 1) {
            $errors['slug'] = 'Slug chỉ gồm chữ thường không dấu, số và dấu gạch ngang (-).';
        } elseif ($this->postModel->slugExists($slug, $ignoreId)) {
            $errors['slug'] = 'Đường dẫn slug này đã tồn tại, vui lòng chọn slug khác.';
        }

        if (mb_strlen($summary, 'UTF-8') > 500) {
            $errors['summary'] = 'Tóm tắt ngắn tối đa 500 ký tự.';
        }

        $plainContent = trim(preg_replace('/\s+/u', ' ', strip_tags($content)) ?? '');
        if (mb_strlen($plainContent, 'UTF-8') < 20) {
            $errors['content'] = 'Nội dung bài viết phải có ít nhất 20 ký tự.';
        }

        if (mb_strlen($metaTitle, 'UTF-8') > 255) {
            $errors['meta_title'] = 'Meta Title tối đa 255 ký tự.';
        }
        if (mb_strlen($metaDesc, 'UTF-8') > 300) {
            $errors['meta_description'] = 'Meta Description tối đa 300 ký tự.';
        }
        if (mb_strlen($metaKeywords, 'UTF-8') > 255) {
            $errors['meta_keywords'] = 'Meta Keywords tối đa 255 ký tự.';
        }

        return [[
            'title'            => $title,
            'slug'             => $slug,
            'summary'          => $summary,
            'content'          => $content,
            'meta_title'       => $metaTitle,
            'meta_description' => $metaDesc,
            'meta_keywords'    => $metaKeywords,
            'status'           => $status,
        ], $errors];
    }

    private function handleImageUpload(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return [null, 'Tải ảnh lên thất bại (mã lỗi: ' . (int) ($file['error'] ?? -1) . ').'];
        }

        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > 2 * 1024 * 1024) {
            return [null, 'Dung lượng ảnh không hợp lệ (tối đa 2MB).'];
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');
        if ($tmpName === '' || !is_uploaded_file($tmpName)) {
            return [null, 'File upload không hợp lệ.'];
        }

        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($tmpName);
        if (!isset($allowedMimes[$mime]) || @getimagesize($tmpName) === false) {
            return [null, 'Chỉ chấp nhận file ảnh thật định dạng JPG, PNG hoặc WEBP.'];
        }

        $uploadDir = dirname(__DIR__, 3) . '/public/uploads/posts';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = 'post_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $allowedMimes[$mime];
        $destPath = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($tmpName, $destPath)) {
            return [null, 'Không thể lưu file ảnh lên máy chủ.'];
        }

        return ['uploads/posts/' . $filename, null];
    }

    private function removeUploadedFile(string $relativePath): void
    {
        $normalized = ltrim(str_replace('\\', '/', $relativePath), '/');
        if (strpos($normalized, 'uploads/posts/') !== 0) {
            return;
        }
        $fullPath = dirname(__DIR__, 3) . '/public/' . $normalized;
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    private function sanitizeHtml(string $html): string
    {
        $clean = preg_replace('#<(script|style|iframe|object|embed|form)\b[^>]*>.*?</\1>#is', '', $html) ?? $html;
        $clean = preg_replace('#<(script|style|iframe|object|embed|form)\b[^>]*\/?>#is', '', $clean) ?? $clean;
        $clean = strip_tags($clean, '<p><br><b><strong><i><em><u><h2><h3><h4><ul><ol><li><blockquote><a><img><table><thead><tbody><tr><th><td><hr><span><div>');
        $clean = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean) ?? $clean;
        $clean = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '$1=$2#$2', $clean) ?? $clean;

        return trim($clean);
    }
}
