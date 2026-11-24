<?php

declare(strict_types=1);

/**
 * Trang About/FAQ + form gửi câu hỏi. Tách tên riêng để tránh trùng basename
 * PageController của admin (autoload của repo tìm theo tên file).
 */
final class AboutFaqController extends Controller
{
    public function about(): void
    {
        $sections = PageContent::getByPage('about');
        $seo = $sections['seo'] ?? [];

        // Dùng lại model Post của nhóm #1: chỉ đọc, không ghi đè model.
        $postModel = new Post();
        $latest = [];
        foreach ($postModel->getPublished('', 3, 0) as $post) {
            $latest[] = [
                'slug'          => (string) ($post['slug'] ?? ''),
                'title'         => (string) ($post['title'] ?? ''),
                'thumbnail'     => (string) ($post['thumbnail'] ?? ''),
                'excerpt'       => (string) ($post['summary'] ?? ''),
                'published_at'  => (string) ($post['created_at'] ?? ''),
                // Nhãn cố định theo AGENTS.md; Post::getPublished() không trả category_name.
                'category_name' => 'NEWS',
            ];
        }

        $this->view('client/about/index', [
            'sections'     => $sections,
            'latestPosts'  => $latest,
            'pageTitle'    => $this->seoTitle($seo, 'About Us | DECOR'),
            'metaDesc'     => $this->seoDesc($seo, 'Learn more about DECOR, your partner for home decor and furniture.'),
            'metaKeywords' => 'DECOR, about, furniture, home decor',
            'activeMenu'   => 'about',
            'breadcrumb'   => 'Giới thiệu',
            // ===== #2 About/FAQ: bật header/footer riêng, không ảnh hưởng account.
            'useShell'     => true,
            'headExtra'    => $this->headExtra(
                '/about',
                'assets/images/post-thumbnail-1.jpg',
                $this->seoTitle($seo, 'About Us | DECOR'),
                $this->seoDesc($seo, 'Learn more about DECOR, your partner for home decor and furniture.')
            ),
            'pageScripts'  => [
                'vendor/bootstrap/bootstrap.bundle.min.js',
                'js/swiper-bundle.min.js',
                'js/about-faq.js',
            ],
        ]);
    }

    public function faq(): void
    {
        $this->renderFaq();
    }

    /** POST /faq/ask */
    public function ask(): void
    {
        $token = $_POST['_csrf'] ?? null;
        if (!verify_csrf(is_string($token) ? $token : null)) {
            http_response_code(403);
            $this->renderFaq(['formError' => 'Security token is missing or invalid. Please try again.']);
            return;
        }

        // Honeypot: bot điền ô ẩn thì chặn, không ghi CSDL.
        if ((string) ($_POST['website'] ?? '') !== '') {
            http_response_code(403);
            $this->renderFaq(['formError' => 'Your submission could not be processed.']);
            return;
        }

        $old = [
            'name'    => trim((string) ($_POST['name'] ?? '')),
            'email'   => trim((string) ($_POST['email'] ?? '')),
            'phone'   => trim((string) ($_POST['phone'] ?? '')),
            'subject' => trim((string) ($_POST['subject'] ?? '')),
            'message' => trim((string) ($_POST['message'] ?? '')),
        ];

        $length = static function (string $value): int {
            return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
        };

        $errors = [];
        if ($old['name'] === '') {
            $errors['name'] = 'Please enter your name.';
        } elseif ($length($old['name']) > 100) {
            $errors['name'] = 'Name must be 100 characters or fewer.';
        }
        if ($old['email'] === '') {
            $errors['email'] = 'Please enter your email address.';
        } elseif (filter_var($old['email'], FILTER_VALIDATE_EMAIL) === false || $length($old['email']) > 150) {
            $errors['email'] = 'Please enter a valid email address (150 characters or fewer).';
        }
        // Phone optional: digits, spaces, + and -, max 20 characters.
        if ($old['phone'] !== '' && preg_match('/^[0-9+ .-]{0,20}$/', $old['phone']) !== 1) {
            $errors['phone'] = 'Use only digits, spaces, + or - (20 characters or fewer).';
        }
        if ($old['subject'] === '') {
            $errors['subject'] = 'Please enter a subject.';
        } elseif ($length($old['subject']) > 200) {
            $errors['subject'] = 'Subject must be 200 characters or fewer.';
        }
        if ($old['message'] === '') {
            $errors['message'] = 'Please enter your message.';
        } elseif ($length($old['message']) < 10) {
            $errors['message'] = 'Message must be at least 10 characters.';
        } elseif ($length($old['message']) > 2000) {
            $errors['message'] = 'Message must be 2000 characters or fewer.';
        }

        if ($errors !== []) {
            http_response_code(422);
            $this->renderFaq([
                'errors'    => $errors,
                'old'       => $old,
                'formError' => 'Please correct the highlighted fields.',
            ]);
            return;
        }

        // Ghi thẳng qua FaqQuestion để dòng mới luôn có source='faq'.
        $userId = (int) ($_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? 0);
        $saved = FaqQuestion::create([
            'user_id' => $userId > 0 ? $userId : null,
            'name'    => $old['name'],
            'email'   => $old['email'],
            'phone'   => $old['phone'],
            'subject' => $old['subject'],
            'message' => $old['message'],
        ]);

        if (!$saved) {
            http_response_code(500);
            $this->renderFaq([
                'old'       => $old,
                'formError' => 'Sorry, we could not save your message. Please try again.',
            ]);
            return;
        }

        // Layout client đã tự render flash; không tự hiển thị trong view.
        set_flash('success', 'Thanks, we have received your question');
        $this->redirect('faq');
    }

    /**
     * @param array<string, mixed> $extra
     */
    private function renderFaq(array $extra = []): void
    {
        $sections = PageContent::getByPage('faq');
        $seo = $sections['seo'] ?? [];

        $data = array_merge([
            'sections'     => $sections,
            'faqs'         => Faq::getActive(),
            'settings'     => $this->settings(),
            'errors'       => [],
            'old'          => [],
            'formError'    => '',
            'pageTitle'    => $this->seoTitle($seo, 'FAQs | DECOR'),
            'metaDesc'     => $this->seoDesc($seo, 'Answers to common questions about DECOR products and services.'),
            'metaKeywords' => 'DECOR, FAQ, questions, answers',
            'activeMenu'   => 'faq',
            'breadcrumb'   => 'Hỏi & đáp',
            // ===== #2 About/FAQ: bật header/footer riêng, không ảnh hưởng account.
            'useShell'     => true,
            'headExtra'    => $this->headExtra(
                '/faq',
                'assets/images/post-thumbnail-2.jpg',
                $this->seoTitle($seo, 'FAQs | DECOR'),
                $this->seoDesc($seo, 'Answers to common questions about DECOR products and services.')
            ),
            'pageScripts'  => [
                'vendor/bootstrap/bootstrap.bundle.min.js',
                'js/faq-form.js',
            ],
        ], $extra);

        $this->view('client/faq/index', $data);
    }

    /** @param array<string, mixed> $seo */
    private function seoTitle(array $seo, string $fallback): string
    {
        $title = trim((string) ($seo['title'] ?? ''));
        return $title !== '' ? $title : $fallback;
    }

    /** @param array<string, mixed> $seo */
    private function seoDesc(array $seo, string $fallback): string
    {
        $desc = trim((string) ($seo['content'] ?? ''));
        return $desc !== '' ? $desc : $fallback;
    }

    /** canonical + Open Graph + local CSS, đưa qua slot $headExtra của layout. */
    private function headExtra(string $path, string $ogImage, string $title, string $desc): string
    {
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $host = preg_replace('/[^a-zA-Z0-9.:-]/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        $canonical = ($https ? 'https://' : 'http://') . $host . base_url($path);
        $og = ($https ? 'https://' : 'http://') . $host . asset($ogImage);

        $css = [
            'vendor/bootstrap/bootstrap.min.css',
            'css/swiper-bundle.min.css',
            'css/fonts.css',
            'css/site.css',
            'css/about-faq.css',
        ];
        $html = '<link rel="canonical" href="' . e($canonical) . '">'
              . '<meta property="og:type" content="website">'
              . '<meta property="og:title" content="' . e($title) . '">'
              . '<meta property="og:description" content="' . e($desc) . '">'
              . '<meta property="og:url" content="' . e($canonical) . '">'
              . '<meta property="og:image" content="' . e($og) . '">'
              . '<link rel="icon" type="image/png" href="' . e(asset('images/logo.png')) . '">';
        foreach ($css as $file) {
            $html .= '<link rel="stylesheet" href="' . e(asset($file)) . '">';
        }

        return $html;
    }

    /** Đọc phone/email/address bằng private query, không sửa model Setting của nhóm #1. */
    private function settings(): array
    {
        $result = ['phone' => '', 'email' => '', 'address' => ''];
        $stmt = Database::getInstance()->getConnection()->prepare(
            'SELECT setting_key, setting_value FROM settings
             WHERE setting_key IN (:k1, :k2, :k3)'
        );
        $stmt->bindValue(':k1', 'phone', PDO::PARAM_STR);
        $stmt->bindValue(':k2', 'email', PDO::PARAM_STR);
        $stmt->bindValue(':k3', 'address', PDO::PARAM_STR);
        $stmt->execute();

        foreach ($stmt->fetchAll() as $row) {
            $key = (string) $row['setting_key'];
            if (array_key_exists($key, $result)) {
                $result[$key] = trim((string) $row['setting_value']);
            }
        }

        return $result;
    }
}
