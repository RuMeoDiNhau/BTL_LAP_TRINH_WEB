<?php

declare(strict_types=1);

/** Quản lý nội dung About/FAQ theo khối cố định (không đụng PageController của client). */
final class PageContentController extends Controller
{
    /** Cấu hình khối: label/hint/group/default chỉ phục vụ giao diện admin. */
    private const CONFIG = [
        'about' => [
            'label'  => 'Trang About',
            'active' => 'admin-about',
            'tabs'   => [
                'banner'       => 'Banner & Intro',
                'story'        => 'Story',
                'testimonials' => 'Testimonials',
                'features'     => 'Features',
                'seo'          => 'SEO',
            ],
            'sections' => [
                ['key' => 'hero', 'label' => 'Page banner',
                 'hint' => 'Main heading at the top of the About page.', 'group' => 'banner',
                 'fields' => ['title'],
                 'default' => ['title' => 'About Us', 'content' => '', 'image' => '']],
                ['key' => 'intro_1', 'label' => 'Intro 1',
                 'hint' => 'Intro – column 1 under the banner.', 'group' => 'banner',
                 'fields' => ['content'],
                 'default' => ['title' => '', 'content' => 'DECOR is a destination for anyone who loves beautiful, comfortable living spaces.', 'image' => '']],
                ['key' => 'intro_2', 'label' => 'Intro 2',
                 'hint' => 'Intro – column 2 under the banner.', 'group' => 'banner',
                 'fields' => ['content'],
                 'default' => ['title' => '', 'content' => 'We believe that every small detail helps make a home feel more relaxed and welcoming.', 'image' => '']],
                ['key' => 'intro_3', 'label' => 'Intro 3',
                 'hint' => 'Intro – column 3 under the banner.', 'group' => 'banner',
                 'fields' => ['content'],
                 'default' => ['title' => '', 'content' => 'Our team listens first and stays with you from the initial idea to the final detail.', 'image' => '']],
                ['key' => 'journey', 'label' => 'Story section',
                 'hint' => 'Story section (with image) – the beginning of our journey.', 'group' => 'story',
                 'fields' => ['title', 'content', 'image'],
                 'default' => ['title' => 'The beginning of our journey',
                               'content' => 'DECOR started with a simple wish: to bring thoughtful decor ideas into every home. We keep learning and listening so that every product and every recommendation suits modern everyday life.',
                               'image' => 'assets/images/post-thumbnail-1.jpg']],
                ['key' => 'who_we_are', 'label' => 'Who we are',
                 'hint' => 'Story section (with image) – introduction of the team.', 'group' => 'story',
                 'fields' => ['title', 'content', 'image'],
                 'default' => ['title' => 'Who are we?',
                               'content' => 'We are furniture lovers who care about making a room feel warmer and easier to live in. DECOR works with care, openness and respect for each customer’s choices.',
                               'image' => 'assets/images/post-thumbnail-2.jpg']],
                ['key' => 'testimonial_1', 'label' => 'Testimonial 1',
                 'hint' => 'Testimonial 1 – first quote card.', 'group' => 'testimonials',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'DECOR customer', 'content' => 'The advice was gentle and easy to follow; I felt heard from start to finish.', 'image' => '']],
                ['key' => 'testimonial_2', 'label' => 'Testimonial 2',
                 'hint' => 'Testimonial 2 – second quote card.', 'group' => 'testimonials',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'DECOR partner', 'content' => 'Small but thoughtful suggestions made my living room feel much tidier.', 'image' => '']],
                ['key' => 'testimonial_3', 'label' => 'Testimonial 3',
                 'hint' => 'Testimonial 3 – third quote card.', 'group' => 'testimonials',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Home decor enthusiast', 'content' => 'I like how DECOR pays attention to materials and the way things feel every day.', 'image' => '']],
                ['key' => 'testimonial_4', 'label' => 'Testimonial 4',
                 'hint' => 'Testimonial 4 – fourth quote card.', 'group' => 'testimonials',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'DECOR community member', 'content' => 'The support after my purchase made me feel confident about shopping with DECOR.', 'image' => '']],
                ['key' => 'feature_1', 'label' => 'Feature 1',
                 'hint' => 'Feature 1 – first service highlight card.', 'group' => 'features',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Book An Appointment', 'content' => 'Book a consultation so we can help you in more detail.', 'image' => '']],
                ['key' => 'feature_2', 'label' => 'Feature 2',
                 'hint' => 'Feature 2 – second service highlight card.', 'group' => 'features',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Pick up in store', 'content' => 'Choose your pieces and collect them directly from our store.', 'image' => '']],
                ['key' => 'feature_3', 'label' => 'Feature 3',
                 'hint' => 'Feature 3 – third service highlight card.', 'group' => 'features',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Special packaging', 'content' => 'Every gift is wrapped with care and attention.', 'image' => '']],
                ['key' => 'feature_4', 'label' => 'Feature 4',
                 'hint' => 'Feature 4 – fourth service highlight card.', 'group' => 'features',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Free global returns', 'content' => 'Returns are easy under our standard policy.', 'image' => '']],
                ['key' => 'seo', 'label' => 'SEO',
                 'hint' => 'SEO – title and description for search engines.', 'group' => 'seo',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'About DECOR | DECOR', 'content' => 'Learn more about DECOR, your partner for home decor and furniture.', 'image' => '']],
            ],
        ],
        'faq' => [
            'label'  => 'Trang FAQ (nội dung)',
            'active' => 'admin-faq-page',
            'tabs'   => [
                'banner' => 'Banner & Intro',
                'ask'    => 'Ask us',
                'seo'    => 'SEO',
            ],
            'sections' => [
                ['key' => 'hero', 'label' => 'Page banner',
                 'hint' => 'Page banner – main heading at the top of the FAQ page.', 'group' => 'banner',
                 'fields' => ['title'],
                 'default' => ['title' => 'FAQs', 'content' => '', 'image' => '']],
                ['key' => 'intro', 'label' => 'Intro',
                 'hint' => 'Intro – heading and text under the FAQ banner.', 'group' => 'banner',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Frequently asked questions',
                               'content' => 'Below are the questions we hear most often. If you cannot find the answer you need, please get in touch and we will be happy to help.',
                               'image' => '']],
                ['key' => 'ask', 'label' => 'Ask us',
                 'hint' => 'Ask us – heading and text above the contact form.', 'group' => 'ask',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Ask us anything', 'content' => 'You can also send us a message below — we are always happy to help.', 'image' => '']],
                ['key' => 'seo', 'label' => 'SEO',
                 'hint' => 'SEO – title and description for search engines.', 'group' => 'seo',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'FAQs | DECOR',
                               'content' => 'Answers to common questions about choosing products, delivery, returns and payment at DECOR.',
                               'image' => '']],
            ],
        ],
    ];

    public function edit($page): void
    {
        require_admin();
        $page = (string) $page;
        $config = $this->config($page);
        if ($config === null) {
            http_response_code(404);
            echo '404';
            return;
        }

        $rows = PageContent::getByPage($page);
        $values = [];
        foreach ($config['sections'] as $section) {
            $key = $section['key'];
            $row = $rows[$key] ?? [];
            $values[$key] = [
                'title'   => (string) ($row['title'] ?? ''),
                'content' => (string) ($row['content'] ?? ''),
                'image'   => (string) ($row['image'] ?? ''),
            ];
        }

        $this->renderEdit($page, $config, $values, []);
    }

    public function update($page): void
    {
        require_admin();
        $page = (string) $page;
        $config = $this->config($page);
        if ($config === null) {
            http_response_code(404);
            echo '404';
            return;
        }

        $token = $_POST['_csrf'] ?? null;
        if (!verify_csrf(is_string($token) ? $token : null)) {
            set_flash('error', 'Phiên không hợp lệ, vui lòng thử lại.');
            $this->redirect('admin/pages/' . $page);
            return;
        }

        $rows = PageContent::getByPage($page);
        $values = [];
        $errors = [];
        $uploadedPaths = [];
        $len = static function (string $v): int {
            return function_exists('mb_strlen') ? mb_strlen($v, 'UTF-8') : strlen($v);
        };

        foreach ($config['sections'] as $section) {
            $key = $section['key'];
            $fields = $section['fields'];
            $old = $rows[$key] ?? [];
            $title = in_array('title', $fields, true) ? trim((string) ($_POST['sections'][$key]['title'] ?? '')) : '';
            $content = in_array('content', $fields, true) ? trim((string) ($_POST['sections'][$key]['content'] ?? '')) : '';
            $image = (string) ($old['image'] ?? '');

            if (in_array('title', $fields, true) && $title === '') {
                $errors[$key]['title'] = 'Vui lòng nhập tiêu đề.';
            } elseif ($len($title) > 255) {
                $errors[$key]['title'] = 'Tiêu đề tối đa 255 ký tự.';
            }
            if (in_array('content', $fields, true) && $content === '') {
                $errors[$key]['content'] = 'Vui lòng nhập nội dung.';
            } elseif ($len($content) > 5000) {
                $errors[$key]['content'] = 'Nội dung tối đa 5000 ký tự.';
            }

            if (in_array('image', $fields, true)) {
                $remove = !empty($_POST['sections'][$key]['remove_image']);
                $file = $_FILES['sections']['name'][$key]['image'] ?? null;
                $hasUpload = is_string($file) && $file !== '';

                if ($remove) {
                    $image = '';
                }
                if ($hasUpload) {
                    $upload = $this->uploadImage($key);
                    $image = $upload[0];
                    if ($upload[1] !== null) {
                        $errors[$key]['image'] = $upload[1];
                    } elseif ($image !== '') {
                        // Lưu lại để xóa nếu một khối khác có lỗi validate.
                        $uploadedPaths[] = $image;
                    }
                }
            }

            $values[$key] = ['title' => $title, 'content' => $content, 'image' => $image];
        }

        if ($errors !== []) {
            foreach ($uploadedPaths as $path) {
                @unlink(dirname(__DIR__, 3) . '/public/' . $path);
            }
            $this->renderEdit($page, $config, $values, $errors);
            return;
        }

        foreach ($config['sections'] as $section) {
            $key = $section['key'];
            $fields = $section['fields'];
            $oldImage = (string) (($rows[$key] ?? [])['image'] ?? '');
            $newImage = $values[$key]['image'];

            // Giữ sort_order cũ trong DB; CONFIG chỉ dùng để nhóm tab, không sắp xếp lại.
            PageContent::save($page, [
                'section_key' => $key,
                'title'       => in_array('title', $fields, true) ? $values[$key]['title'] : null,
                'content'     => in_array('content', $fields, true) ? $values[$key]['content'] : null,
                'image'       => in_array('image', $fields, true) ? ($newImage === '' ? null : $newImage) : null,
                'sort_order'  => $index,
            ], !in_array('image', $fields, true), true);

            // Xoá file cũ khi thay/xoá, nhưng không đụng ảnh seed trong assets/images.
            if (in_array('image', $fields, true) && $oldImage !== $newImage
                && strpos($oldImage, 'uploads/about/') === 0) {
                $oldPath = dirname(__DIR__, 3) . '/public/' . $oldImage;
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

        set_flash('success', 'Đã lưu nội dung trang.');
        $this->redirect('admin/pages/' . $page);
    }

    /** @return array{0: string, 1: ?string} [path, error] */
    private function uploadImage(string $key): array
    {
        $file = $_FILES['sections'] ?? null;
        if (!is_array($file) || !isset($file['tmp_name'][$key]['image'])) {
            return ['', 'Không nhận được file ảnh.'];
        }
        $tmp = (string) $file['tmp_name'][$key]['image'];
        $name = (string) ($file['name'][$key]['image'] ?? '');
        $err = (int) ($file['error'][$key]['image'] ?? UPLOAD_ERR_NO_FILE);
        $size = (int) ($file['size'][$key]['image'] ?? 0);

        if ($err !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
            return ['', 'Upload ảnh thất bại.'];
        }
        if ($size > 2 * 1024 * 1024) {
            return ['', 'Ảnh tối đa 2MB.'];
        }

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
        if (!isset($allowed[$ext])) {
            return ['', 'Chỉ chấp nhận jpg, png, webp.'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $tmp) : false;
        if ($finfo) {
            finfo_close($finfo);
        }
        if (!is_string($mime) || !in_array($mime, array_values($allowed), true)) {
            return ['', 'MIME ảnh không hợp lệ.'];
        }

        $dir = dirname(__DIR__, 3) . '/public/uploads/about';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $filename = bin2hex(random_bytes(16)) . '.' . ($ext === 'jpeg' ? 'jpg' : $ext);
        if (!move_uploaded_file($tmp, $dir . '/' . $filename)) {
            return ['', 'Không thể lưu ảnh.'];
        }

        return ['uploads/about/' . $filename, null];
    }

    private function config(string $page): ?array
    {
        return self::CONFIG[$page] ?? null;
    }

    /** @param array<string, mixed> $config @param array<string, array<string,string>> $values @param array<string, array<string,string>> $errors */
    private function renderEdit(string $page, array $config, array $values, array $errors): void
    {
        $this->view('admin/pages/edit', $this->adminData([
            'page'   => $page,
            'config' => $config,
            'values' => $values,
            'errors' => $errors,
        ], (string) $config['label'], (string) $config['active'], 'js/admin-pages.js'), 'admin');
    }

    /** @param array<string, mixed> $data @return array<string, mixed> */
    private function adminData(array $data, string $title, string $active, string $script): array
    {
        return array_merge($data, [
            'pageTitle'    => $title,
            'metaDesc'     => 'Decor admin – quản lý nội dung trang.',
            'metaKeywords' => 'Decor, admin',
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
            // Cột source do nhóm #1 thêm; nếu DB cũ chưa migrate thì vẫn cho sửa nội dung.
            return 0;
        }
    }
}
