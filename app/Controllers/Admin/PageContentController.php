<?php

declare(strict_types=1);

/** Quản lý nội dung About/FAQ theo khối cố định. */
final class PageContentController extends Controller
{
    /** Cấu hình khối: label/hint/group/default chỉ phục vụ giao diện admin. */
    private const CONFIG = [
        'about' => [
            'label'  => 'Trang Giới thiệu',
            'active' => 'admin-about',
            'tabs'   => [
                'banner'       => 'Banner & Giới thiệu',
                'story'        => 'Câu chuyện',
                'testimonials' => 'Chứng thực',
                'features'     => 'Tiện ích',
                'seo'          => 'SEO',
            ],
            'sections' => [
                ['key' => 'hero', 'label' => 'Banner trang',
                 'hint' => 'Tiêu đề chính ở đầu trang Giới thiệu.', 'group' => 'banner',
                 'fields' => ['title'],
                 'default' => ['title' => 'Về chúng tôi', 'content' => '', 'image' => '']],
                ['key' => 'intro_1', 'label' => 'Giới thiệu 1',
                 'hint' => 'Giới thiệu – cột 1 bên dưới banner.', 'group' => 'banner',
                 'fields' => ['content'],
                 'default' => ['title' => '', 'content' => 'DECOR là điểm đến dành cho những ai yêu thích không gian sống đẹp và thoải mái.', 'image' => '']],
                ['key' => 'intro_2', 'label' => 'Giới thiệu 2',
                 'hint' => 'Giới thiệu – cột 2 bên dưới banner.', 'group' => 'banner',
                 'fields' => ['content'],
                 'default' => ['title' => '', 'content' => 'Chúng tôi tin rằng mỗi chi tiết nhỏ đều góp phần tạo nên một ngôi nhà thư giãn và ấm cúng hơn.', 'image' => '']],
                ['key' => 'intro_3', 'label' => 'Giới thiệu 3',
                 'hint' => 'Giới thiệu – cột 3 bên dưới banner.', 'group' => 'banner',
                 'fields' => ['content'],
                 'default' => ['title' => '', 'content' => 'Đội ngũ của chúng tôi luôn lắng nghe trước và đồng hành cùng bạn từ ý tưởng đầu tiên đến từng chi tiết cuối cùng.', 'image' => '']],
                ['key' => 'journey', 'label' => 'Phần câu chuyện',
                 'hint' => 'Câu chuyện (kèm hình ảnh) – khởi đầu hành trình của chúng tôi.', 'group' => 'story',
                 'fields' => ['title', 'content', 'image'],
                 'default' => ['title' => 'Khởi đầu hành trình',
                               'content' => 'DECOR bắt đầu từ một mong muốn giản dị: mang những ý tưởng trang trí tinh tế đến với mọi ngôi nhà. Chúng tôi không ngừng học hỏi và lắng nghe để mỗi sản phẩm, mỗi gợi ý đều phù hợp với nhịp sống hiện đại.',
                               'image' => 'assets/images/post-thumbnail-1.jpg']],
                ['key' => 'who_we_are', 'label' => 'Về chúng tôi',
                 'hint' => 'Câu chuyện (kèm hình ảnh) – giới thiệu về đội ngũ.', 'group' => 'story',
                 'fields' => ['title', 'content', 'image'],
                 'default' => ['title' => 'Chúng tôi là ai?',
                               'content' => 'Chúng tôi là những người yêu nội thất, mong muốn mang đến cho mỗi căn phòng sự ấm áp và dễ sống hơn. DECOR làm việc bằng sự tận tâm, cởi mở và tôn trọng lựa chọn của từng khách hàng.',
                               'image' => 'assets/images/post-thumbnail-2.jpg']],
                ['key' => 'testimonial_1', 'label' => 'Chứng thực 1',
                 'hint' => 'Chứng thực 1 – thẻ nhận xét thứ nhất.', 'group' => 'testimonials',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Khách hàng DECOR', 'content' => 'Lời tư vấn nhẹ nhàng và dễ áp dụng; tôi thấy mình được lắng nghe từ đầu đến cuối.', 'image' => '']],
                ['key' => 'testimonial_2', 'label' => 'Chứng thực 2',
                 'hint' => 'Chứng thực 2 – thẻ nhận xét thứ hai.', 'group' => 'testimonials',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Đối tác DECOR', 'content' => 'Những gợi ý nhỏ nhưng tinh tế giúp phòng khách của tôi gọn gàng hơn hẳn.', 'image' => '']],
                ['key' => 'testimonial_3', 'label' => 'Chứng thực 3',
                 'hint' => 'Chứng thực 3 – thẻ nhận xét thứ ba.', 'group' => 'testimonials',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Người yêu trang trí nhà', 'content' => 'Tôi thích cách DECOR chú ý đến chất liệu và cảm giác sử dụng hằng ngày.', 'image' => '']],
                ['key' => 'testimonial_4', 'label' => 'Chứng thực 4',
                 'hint' => 'Chứng thực 4 – thẻ nhận xét thứ tư.', 'group' => 'testimonials',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Thành viên cộng đồng DECOR', 'content' => 'Sự hỗ trợ sau khi mua hàng khiến tôi hoàn toàn yên tâm khi chọn DECOR.', 'image' => '']],
                ['key' => 'feature_1', 'label' => 'Tiện ích 1',
                 'hint' => 'Tiện ích 1 – thẻ nổi bật thứ nhất về dịch vụ.', 'group' => 'features',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Đặt lịch hẹn', 'content' => 'Đặt lịch tư vấn để chúng tôi hỗ trợ bạn chi tiết hơn.', 'image' => '']],
                ['key' => 'feature_2', 'label' => 'Tiện ích 2',
                 'hint' => 'Tiện ích 2 – thẻ nổi bật thứ hai về dịch vụ.', 'group' => 'features',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Nhận hàng tại cửa hàng', 'content' => 'Chọn sản phẩm và nhận trực tiếp tại cửa hàng của chúng tôi.', 'image' => '']],
                ['key' => 'feature_3', 'label' => 'Tiện ích 3',
                 'hint' => 'Tiện ích 3 – thẻ nổi bật thứ ba về dịch vụ.', 'group' => 'features',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Đóng gói quà tặng', 'content' => 'Món quà nào cũng được gói bằng sự cẩn trọng và tinh tế.', 'image' => '']],
                ['key' => 'feature_4', 'label' => 'Tiện ích 4',
                 'hint' => 'Tiện ích 4 – thẻ nổi bật thứ tư về dịch vụ.', 'group' => 'features',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Trả hàng miễn phí toàn cầu', 'content' => 'Trả hàng dễ dàng theo chính sách chung của chúng tôi.', 'image' => '']],
                ['key' => 'seo', 'label' => 'SEO',
                 'hint' => 'SEO – tiêu đề và mô tả cho công cụ tìm kiếm.', 'group' => 'seo',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Về DECOR | DECOR', 'content' => 'Tìm hiểu về DECOR, người bạn đồng hành trong trang trí và nội thất nhà ở.', 'image' => '']],
            ],
        ],
        'faq' => [
            'label'  => 'Trang FAQ (nội dung)',
            'active' => 'admin-faq-page',
            'tabs'   => [
                'banner' => 'Banner & Giới thiệu',
                'ask'    => 'Gửi câu hỏi',
                'seo'    => 'SEO',
            ],
            'sections' => [
                ['key' => 'hero', 'label' => 'Banner trang',
                 'hint' => 'Banner – tiêu đề chính ở đầu trang FAQ.', 'group' => 'banner',
                 'fields' => ['title'],
                 'default' => ['title' => 'Câu hỏi thường gặp', 'content' => '', 'image' => '']],
                ['key' => 'intro', 'label' => 'Giới thiệu',
                 'hint' => 'Giới thiệu – tiêu đề và nội dung bên dưới banner FAQ.', 'group' => 'banner',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Các câu hỏi thường gặp',
                               'content' => 'Dưới đây là những câu hỏi chúng tôi nhận được nhiều nhất. Nếu chưa tìm thấy câu trả lời, hãy liên hệ để được hỗ trợ.',
                               'image' => '']],
                ['key' => 'ask', 'label' => 'Gửi câu hỏi',
                 'hint' => 'Gửi câu hỏi – tiêu đề và nội dung phía trên biểu mẫu liên hệ.', 'group' => 'ask',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Đặt câu hỏi cho chúng tôi',
                               'content' => 'Bạn cũng có thể gửi tin nhắn bên dưới — chúng tôi luôn sẵn lòng giúp đỡ.', 'image' => '']],
                ['key' => 'seo', 'label' => 'SEO',
                 'hint' => 'SEO – tiêu đề và mô tả cho công cụ tìm kiếm.', 'group' => 'seo',
                 'fields' => ['title', 'content'],
                 'default' => ['title' => 'Câu hỏi thường gặp | DECOR',
                               'content' => 'Giải đáp các câu hỏi thường gặp về lựa chọn sản phẩm, giao hàng, trả hàng và thanh toán tại DECOR.',
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

        foreach ($config['sections'] as $index => $section) {
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
            // Cột source do #1 thêm; nếu DB cũ chưa migrate thì vẫn cho sửa nội dung.
            return 0;
        }
    }
}
