<?php

declare(strict_types=1);

class PageController extends Controller
{
    public function contact(): void
    {
        $settings = (new Setting())->all();
        $errors = [];
        $old = [
            'name' => '',
            'email' => '',
            'phone' => '',
            'subject' => '',
            'message' => '',
        ];

        if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'POST') {
            if (!verify_csrf(isset($_POST['_csrf']) ? (string) $_POST['_csrf'] : null)) {
                $errors['_form'] = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng tải lại trang và thử lại.';
            }

            foreach ($old as $key => $_value) {
                $old[$key] = trim((string) ($_POST[$key] ?? ''));
            }

            if (mb_strlen($old['name'], 'UTF-8') < 2 || mb_strlen($old['name'], 'UTF-8') > 100) {
                $errors['name'] = 'Họ tên cần có từ 2 đến 100 ký tự.';
            }
            if (filter_var($old['email'], FILTER_VALIDATE_EMAIL) === false || mb_strlen($old['email'], 'UTF-8') > 150) {
                $errors['email'] = 'Vui lòng nhập email hợp lệ.';
            }
            if (mb_strlen($old['phone'], 'UTF-8') > 20) {
                $errors['phone'] = 'Số điện thoại tối đa 20 ký tự.';
            }
            if (mb_strlen($old['subject'], 'UTF-8') > 200) {
                $errors['subject'] = 'Chủ đề tối đa 200 ký tự.';
            }
            if ($old['message'] === '' || mb_strlen($old['message'], 'UTF-8') > 5000) {
                $errors['message'] = 'Nội dung không được để trống và tối đa 5000 ký tự.';
            }

            if ($errors === []) {
                $user = current_user();
                (new Contact())->create([
                    'user_id' => $user === null ? null : (int) $user['id'],
                    'name' => $old['name'],
                    'email' => mb_strtolower($old['email'], 'UTF-8'),
                    'phone' => $old['phone'] === '' ? null : $old['phone'],
                    'subject' => $old['subject'] === '' ? null : $old['subject'],
                    'message' => $old['message'],
                ]);
                set_flash('success', 'Thông tin liên hệ của bạn đã được gửi.');
                $this->redirect('/contact');
            }
        }

        $this->view('client/contact/index', [
            'pageTitle' => 'Liên hệ | ' . (string) ($settings['site_name'] ?? 'Decor'),
            'settings' => $settings,
            'errors' => $errors,
            'old' => $old,
            'flashSuccess' => get_flash('success'),
        ], null);
    }
}
