<?php

declare(strict_types=1);

class PostController extends Controller
{
    private Post $postModel;
    private Comment $commentModel;

    public function __construct(?Post $postModel = null, ?Comment $commentModel = null)
    {
        $this->postModel = $postModel ?? new Post();
        $this->commentModel = $commentModel ?? new Comment();
    }

    public function index(): void
    {
        $keyword = trim((string) ($_GET['keyword'] ?? ''));
        if (mb_strlen($keyword, 'UTF-8') > 100) {
            $keyword = mb_substr($keyword, 0, 100, 'UTF-8');
        }

        $perPage = 6;
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $totalPosts = $this->postModel->countPublished($keyword);
        $totalPages = max(1, (int) ceil($totalPosts / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        $posts = $this->postModel->getPublished($keyword, $perPage, $offset);
        $latestPosts = $this->postModel->getPublished('', 4, 0);

        $pageTitle = $keyword !== ''
            ? 'Tìm kiếm bài viết: ' . $keyword . ' | Decor E-Commerce'
            : 'Tin tức & Cẩm nang Nội thất | Decor E-Commerce';

        $this->view('client/posts/index', [
            'pageTitle'    => $pageTitle,
            'metaDesc'     => 'Tin tức, xu hướng thiết kế nội thất và cẩm nang trang trí không gian sống từ Decor E-Commerce.',
            'metaKeywords' => 'tin tức nội thất, trang trí phòng khách, decor nhà đẹp, xu hướng nội thất',
            'headExtra'    => '<link rel="stylesheet" href="' . e(asset('css/fonts.css')) . '">' . "\n"
                . '<link rel="stylesheet" href="' . e(asset('css/posts.css')) . '">',
            'pageScripts'  => ['js/posts.js'],
            'posts'        => $posts,
            'latestPosts'  => $latestPosts,
            'keyword'      => $keyword,
            'page'         => $page,
            'totalPages'   => $totalPages,
            'totalPosts'   => $totalPosts,
        ], 'layouts/client');
    }

    public function show(string $slug): void
    {
        $post = $this->postModel->findPublishedBySlug($slug);
        if ($post === null) {
            http_response_code(404);
            $errorView = dirname(__DIR__, 2) . '/Views/errors/404.php';
            if (is_file($errorView)) {
                require $errorView;
                return;
            }
            echo '404 – Không tìm thấy bài viết.';
            return;
        }

        $postId = (int) $post['id'];

        if (empty($_SESSION['viewed_posts'][$postId])) {
            $this->postModel->incrementViews($postId);
            $_SESSION['viewed_posts'][$postId] = true;
            $post['views'] = (int) $post['views'] + 1;
        }

        $perPage = 10;
        $cpage = max(1, (int) ($_GET['cpage'] ?? 1));
        $totalComments = $this->commentModel->countApprovedByPost($postId);
        $totalCommentPages = max(1, (int) ceil($totalComments / $perPage));
        if ($cpage > $totalCommentPages) {
            $cpage = $totalCommentPages;
        }
        $offset = ($cpage - 1) * $perPage;

        $comments = $this->commentModel->getApprovedByPost($postId, $perPage, $offset);
        $latestPosts = $this->postModel->getPublished('', 4, 0);

        $oldComment = (string) ($_SESSION['old_comment'] ?? '');
        unset($_SESSION['old_comment']);

        $metaTitle = !empty($post['meta_title']) ? (string) $post['meta_title'] : (string) $post['title'];
        $metaDesc = !empty($post['meta_description'])
            ? (string) $post['meta_description']
            : (string) ($post['summary'] ?? '');
        $metaKeywords = !empty($post['meta_keywords'])
            ? (string) $post['meta_keywords']
            : 'Decor, tin tức nội thất';

        $ogTags = '<link rel="stylesheet" href="' . e(asset('css/fonts.css')) . '">' . "\n"
            . '<link rel="stylesheet" href="' . e(asset('css/posts.css')) . '">' . "\n"
            . '<meta property="og:type" content="article">' . "\n"
            . '<meta property="og:title" content="' . e($metaTitle) . '">' . "\n"
            . '<meta property="og:description" content="' . e($metaDesc) . '">';

        $this->view('client/posts/show', [
            'pageTitle'         => $metaTitle . ' | Decor E-Commerce',
            'metaDesc'          => $metaDesc,
            'metaKeywords'      => $metaKeywords,
            'headExtra'         => $ogTags,
            'pageScripts'       => ['js/posts.js'],
            'post'              => $post,
            'comments'          => $comments,
            'totalComments'     => $totalComments,
            'cpage'             => $cpage,
            'totalCommentPages' => $totalCommentPages,
            'latestPosts'       => $latestPosts,
            'oldComment'        => $oldComment,
        ], 'layouts/client');
    }

    public function storeComment(string $slug): void
    {
        require_login();

        if (!verify_csrf($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Phiên biểu mẫu không hợp lệ (CSRF). Vui lòng thử lại.');
            $this->redirect('/posts/' . rawurlencode($slug) . '#comments');
        }

        $post = $this->postModel->findPublishedBySlug($slug);
        if ($post === null) {
            set_flash('error', 'Bài viết không tồn tại hoặc đã bị gỡ.');
            $this->redirect('/posts');
        }

        $content = trim((string) ($_POST['content'] ?? ''));
        $len = mb_strlen($content, 'UTF-8');

        if ($len < 5 || $len > 1000) {
            $_SESSION['old_comment'] = $content;
            set_flash('error', 'Nội dung bình luận phải từ 5 đến 1000 ký tự.');
            $this->redirect('/posts/' . rawurlencode($slug) . '#comments');
        }

        $user = current_user();
        $moderation = $this->commentModel->isModerationEnabled();
        $status = $moderation ? 'pending' : 'approved';

        $this->commentModel->create((int) $post['id'], (int) $user['id'], $content, $status);

        set_flash(
            'success',
            $moderation
                ? 'Bình luận của bạn đã được gửi và đang chờ Quản trị viên duyệt.'
                : 'Bình luận của bạn đã được đăng thành công!'
        );
        $this->redirect('/posts/' . rawurlencode($slug) . '#comments');
    }
}
