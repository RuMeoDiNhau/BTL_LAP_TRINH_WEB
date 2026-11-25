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

        $postModel = new Post();
        $latest = [];
        foreach ($postModel->getPublished('', 3, 0) as $post) {
            $latest[] = [
                'slug'          => (string) ($post['slug'] ?? ''),
                'title'         => (string) ($post['title'] ?? ''),
                'thumbnail'     => (string) ($post['thumbnail'] ?? ''),
                'excerpt'       => (string) ($post['summary'] ?? ''),
                'published_at'  => (string) ($post['created_at'] ?? ''),
                'category_name' => 'NEWS',
            ];
        }

        $this->view('client/about/index', [
            'sections'     => $sections,
            'latestPosts'  => $latest,
            'pageTitle'    => $this->seoTitle($seo, 'Giới thiệu | DECOR'),
            'metaDesc'     => $this->seoDesc($seo, 'Tìm hiểu về DECOR, người bạn đồng hành trong trang trí và nội thất nhà ở.'),
            'metaKeywords' => 'DECOR, giới thiệu, nội thất, trang trí nhà',
            'activeMenu'   => 'about',

            'useShell'     => true,
            'headExtra'    => $this->headExtra(
                '/about',
                'images/post-thumbnail-1.jpg',
                $this->seoTitle($seo, 'Giới thiệu | DECOR'),
                $this->seoDesc($seo, 'Tìm hiểu về DECOR, người bạn đồng hành trong trang trí và nội thất nhà ở.')
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
            $this->renderFaq(['formError' => 'Mã bảo mật không hợp lệ hoặc đã hết hạn. Vui lòng thử lại.']);
            return;
        }

        // Honeypot: bot điền ô ẩn thì chặn, không ghi CSDL.
        if ((string) ($_POST['website'] ?? '') !== '') {
            http_response_code(403);
            $this->renderFaq(['formError' => 'Không thể xử lý biểu mẫu. Vui lòng thử lại.']);
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
            $errors['name'] = 'Vui lòng nhập họ và tên.';
        } elseif ($length($old['name']) > 100) {
            $errors['name'] = 'Họ và tên tối đa 100 ký tự.';
        }
        if ($old['email'] === '') {
            $errors['email'] = 'Vui lòng nhập địa chỉ email.';
        } elseif (filter_var($old['email'], FILTER_VALIDATE_EMAIL) === false || $length($old['email']) > 150) {
            $errors['email'] = 'Vui lòng nhập địa chỉ email hợp lệ (tối đa 150 ký tự).';
        }
        // Số điện thoại không bắt buộc: chỉ chữ số, dấu cách, + và -, tối đa 20 ký tự.
        if ($old['phone'] !== '' && preg_match('/^[0-9+ .-]{0,20}$/', $old['phone']) !== 1) {
            $errors['phone'] = 'Chỉ dùng chữ số, dấu cách, + hoặc - (tối đa 20 ký tự).';
        }
        if ($old['subject'] === '') {
            $errors['subject'] = 'Vui lòng nhập chủ đề.';
        } elseif ($length($old['subject']) > 200) {
            $errors['subject'] = 'Chủ đề tối đa 200 ký tự.';
        }
        if ($old['message'] === '') {
            $errors['message'] = 'Vui lòng nhập nội dung tin nhắn.';
        } elseif ($length($old['message']) < 10) {
            $errors['message'] = 'Tin nhắn phải có ít nhất 10 ký tự.';
        } elseif ($length($old['message']) > 2000) {
            $errors['message'] = 'Tin nhắn tối đa 2000 ký tự.';
        }

        if ($errors !== []) {
            http_response_code(422);
            $this->renderFaq([
                'errors'    => $errors,
                'old'       => $old,
                'formError' => 'Vui lòng kiểm tra các trường được đánh dấu.',
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
                'formError' => 'Xin lỗi, không thể lưu tin nhắn. Vui lòng thử lại.',
            ]);
            return;
        }

        // Layout client đã tự render flash; không tự hiển thị trong view.
        set_flash('success', 'Cảm ơn bạn, chúng tôi đã nhận được câu hỏi.');
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
            'pageTitle'    => $this->seoTitle($seo, 'Câu hỏi thường gặp | DECOR'),
            'metaDesc'     => $this->seoDesc($seo, 'Giải đáp các câu hỏi thường gặp về sản phẩm và dịch vụ của DECOR.'),
            'metaKeywords' => 'DECOR, câu hỏi thường gặp, giải đáp, hỗ trợ',
            'activeMenu'   => 'faq',
            // ===== #2 About/FAQ: bật header/footer riêng, không ảnh hưởng account.
            'useShell'     => true,
            'headExtra'    => $this->headExtra(
                '/faq',
                'images/post-thumbnail-2.jpg',
                $this->seoTitle($seo, 'Câu hỏi thường gặp | DECOR'),
                $this->seoDesc($seo, 'Giải đáp các câu hỏi thường gặp về sản phẩm và dịch vụ của DECOR.')
            ),
            'pageScripts'  => [
                'vendor/bootstrap/bootstrap.bundle.min.js',
                'js/about-faq.js',
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

    /** Đọc phone/email/address bằng private query. */
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
