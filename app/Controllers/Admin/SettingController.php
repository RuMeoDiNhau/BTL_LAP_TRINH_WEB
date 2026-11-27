<?php

declare(strict_types=1);

class SettingController extends Controller
{
    public function __construct()
    {
        require_admin();
    }

    public function index(): void
    {
        $this->renderForm([], []);
    }

    public function update(): void
    {
        if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST'
            || !verify_csrf(isset($_POST['_csrf']) ? (string) $_POST['_csrf'] : null)) {
            http_response_code(400);
            set_flash('error', 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.');
            $this->redirect('/admin/settings');
        }

        $settings = [
            'site_name' => trim((string) ($_POST['site_name'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'address' => trim((string) ($_POST['address'] ?? '')),
        ];
        $sections = [];
        foreach (['hero', 'welcome', 'about'] as $key) {
            $sections[$key] = [
                'title' => trim((string) ($_POST[$key . '_title'] ?? '')),
                'content' => trim((string) ($_POST[$key . '_content'] ?? '')),
            ];
        }

        $errors = [];
        if ($settings['site_name'] === '' || mb_strlen($settings['site_name'], 'UTF-8') > 100) {
            $errors['site_name'] = 'Tên website cần có từ 1 đến 100 ký tự.';
        }
        if (mb_strlen($settings['phone'], 'UTF-8') > 20) {
            $errors['phone'] = 'Số điện thoại tối đa 20 ký tự.';
        }
        if ($settings['email'] !== '' && (filter_var($settings['email'], FILTER_VALIDATE_EMAIL) === false
            || mb_strlen($settings['email'], 'UTF-8') > 150)) {
            $errors['email'] = 'Vui lòng nhập email hợp lệ, tối đa 150 ký tự.';
        }
        if (mb_strlen($settings['address'], 'UTF-8') > 255) {
            $errors['address'] = 'Địa chỉ tối đa 255 ký tự.';
        }
        foreach ($sections as $key => $section) {
            if (mb_strlen($section['title'], 'UTF-8') > 255) {
                $errors[$key . '_title'] = 'Tiêu đề tối đa 255 ký tự.';
            }
            if (mb_strlen($section['content'], 'UTF-8') > 5000) {
                $errors[$key . '_content'] = 'Nội dung tối đa 5000 ký tự.';
            }
        }

        $uploads = [];
        $uploadSpecs = [];
        foreach (['hero_image', 'welcome_image', 'about_image', 'logo'] as $field) {
            [$spec, $error] = $this->inspectImage($field);
            if ($error !== null) {
                $errors[$field] = $error;
            }
            if ($spec !== null) {
                $uploadSpecs[$field] = $spec;
            }
        }

        if ($errors !== []) {
            $this->renderForm($sections, $errors, $settings, $uploads);
            return;
        }

        foreach ($uploadSpecs as $field => $spec) {
            [$path, $error] = $this->saveImage($spec);
            if ($error !== null) {
                $errors[$field] = $error;
            } elseif ($path !== null) {
                $uploads[$field] = $path;
            }
        }
        if ($errors !== []) {
            $this->renderForm($sections, $errors, $settings, $uploads);
            return;
        }

        foreach ($sections as $key => $section) {
            $imageKey = $key . '_image';
            PageContent::save('home', [
                'section_key' => $key,
                'title' => $section['title'],
                'content' => $section['content'],
                'image' => $uploads[$imageKey] ?? null,
                'sort_order' => 0,
            ], !isset($uploads[$imageKey]), true);
        }
        if (isset($uploads['logo'])) {
            $settings['logo'] = $uploads['logo'];
        }
        (new Setting())->save($settings);

        set_flash('success', 'Đã lưu nội dung trang và thông tin liên hệ.');
        $this->redirect('/admin/settings');
    }

    /** @param array<string,array<string,string>> $sections
     *  @param array<string,string> $settings
     *  @param array<string,string> $uploads */
    private function renderForm(array $sections, array $errors, array $settings = [], array $uploads = []): void
    {
        $storedSections = PageContent::getByPage('home');
        $storedSettings = (new Setting())->all();
        foreach (['hero', 'welcome', 'about'] as $key) {
            $sections[$key] = array_merge($storedSections[$key] ?? ['title' => '', 'content' => '', 'image' => null], $sections[$key] ?? []);
            if (isset($uploads[$key . '_image'])) {
                $sections[$key]['image'] = $uploads[$key . '_image'];
            }
        }
        $settings = array_merge($storedSettings, $settings);
        if (isset($uploads['logo'])) {
            $settings['logo'] = $uploads['logo'];
        }

        $this->view('admin/settings/index', [
            'pageTitle' => 'Nội dung trang | Decor Admin',
            'activeMenu' => 'settings',
            'breadcrumb' => 'Nội dung trang',
            'sections' => $sections,
            'settings' => $settings,
            'errors' => $errors,
        ], 'admin');
    }

    /** @return array{0:?array{tmp_name:string,extension:string},1:?string} */
    private function inspectImage(string $field): array
    {
        $file = $_FILES[$field] ?? null;
        if (!is_array($file) || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return [null, null];
        }
        if ((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
            || !isset($file['tmp_name']) || !is_uploaded_file((string) $file['tmp_name'])) {
            return [null, 'Không thể nhận tệp ảnh đã chọn.'];
        }
        if ((int) ($file['size'] ?? 0) < 1 || (int) $file['size'] > 4 * 1024 * 1024) {
            return [null, 'Ảnh phải có dung lượng tối đa 4 MB.'];
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file((string) $file['tmp_name']);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!is_string($mime) || !isset($extensions[$mime]) || @getimagesize((string) $file['tmp_name']) === false) {
            return [null, 'Chỉ nhận ảnh JPG, PNG hoặc WebP hợp lệ.'];
        }

        return [['tmp_name' => (string) $file['tmp_name'], 'extension' => $extensions[$mime]], null];
    }

    /** @param array{tmp_name:string,extension:string} $spec
     *  @return array{0:?string,1:?string} */
    private function saveImage(array $spec): array
    {
        $directory = dirname(__DIR__, 3) . '/public/uploads/banners';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            return [null, 'Không thể tạo thư mục lưu ảnh trên máy chủ.'];
        }
        $filename = bin2hex(random_bytes(16)) . '.' . $spec['extension'];
        if (!move_uploaded_file($spec['tmp_name'], $directory . '/' . $filename)) {
            return [null, 'Không thể lưu ảnh lên máy chủ.'];
        }
        return ['uploads/banners/' . $filename, null];
    }
}
