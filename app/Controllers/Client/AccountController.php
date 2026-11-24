<?php

declare(strict_types=1);

class AccountController extends Controller
{
    public function profile(array $errors = [], ?array $old = null, int $status = 200): void
    {
        $user = $this->account();
        $this->accountView('client/account/profile', [
            'pageTitle' => 'Hồ sơ cá nhân | Decor', 'active' => 'profile', 'user' => $user,
            'errors' => $errors, 'old' => $old ?? $user,
        ], $status);
    }

    public function updateProfile(): void
    {
        $user = $this->account(true);
        if (!$this->checkFormToken()) {
            return;
        }
        $data = [
            'full_name' => trim($this->input('full_name')),
            'email' => mb_strtolower(trim($this->input('email')), 'UTF-8'),
            'phone' => trim($this->input('phone')),
        ];
        $errors = AccountValidation::profile($data);
        $model = new User();
        if (!isset($errors['email']) && $model->emailExists($data['email'], (int) $user['id'])) {
            $errors['email'] = 'Email này đã được tài khoản khác sử dụng.';
        }
        // Email là thông tin đăng nhập: yêu cầu mật khẩu hiện tại khi thay đổi.
        if (!isset($errors['email']) && $data['email'] !== mb_strtolower($user['email'], 'UTF-8')
            && !password_verify($this->input('email_password'), $user['password_hash'])) {
            $errors['email_password'] = 'Nhập đúng mật khẩu hiện tại để đổi email đăng nhập.';
        }
        if ($errors) {
            $this->profile($errors, $data, 422);
            return;
        }
        try {
            $model->updateProfile((int) $user['id'], $data);
        } catch (PDOException $exception) {
            if (($exception->errorInfo[1] ?? null) !== 1062) {
                throw $exception;
            }
            $this->profile(['email' => 'Email này đã được tài khoản khác sử dụng.'], $data, 422);
            return;
        }
        set_flash('success', 'Đã lưu thông tin cá nhân của bạn.');
        $this->redirectAfterSave('/account/profile');
    }

    public function password(array $errors = [], int $status = 200): void
    {
        $this->accountView('client/account/change-password', [
            'pageTitle' => 'Đổi mật khẩu | Decor', 'active' => 'password',
            'user' => $this->account(), 'errors' => $errors,
        ], $status);
    }

    public function updatePassword(): void
    {
        $user = $this->account(true);
        if (!$this->checkFormToken()) {
            return;
        }
        $password = $this->input('password');
        $errors = AccountValidation::password($password, $this->input('password_confirmation'));
        if (!password_verify($this->input('current_password'), $user['password_hash'])) {
            $errors['current_password'] = 'Mật khẩu hiện tại không chính xác.';
        }
        if (!isset($errors['password']) && password_verify($password, $user['password_hash'])) {
            $errors['password'] = 'Mật khẩu mới phải khác mật khẩu hiện tại.';
        }
        if ($errors) {
            $this->password($errors, 422);
            return;
        }
        $newHash = password_hash($password, PASSWORD_BCRYPT);
        if (!(new User())->updatePassword((int) $user['id'], $user['password_hash'], $newHash)) {
            sign_out();
            set_flash('error', 'Mật khẩu đã thay đổi ở một phiên khác. Vui lòng đăng nhập lại.');
            $this->redirect('/login');
        }
        $user['password_hash'] = $newHash;
        sign_in($user);
        set_flash('success', 'Đã đổi mật khẩu. Các phiên đăng nhập khác sẽ cần đăng nhập lại.');
        $this->redirectAfterSave('/account/change-password');
    }

    public function updateAvatar(): void
    {
        $user = $this->account();
        $postLimit = trim(ini_get('post_max_size'));
        $postBytes = (int) ((float) $postLimit * match (strtolower(substr($postLimit, -1))) {
            'g' => 1073741824, 'm' => 1048576, 'k' => 1024, default => 1,
        });
        if ($postBytes > 0 && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > $postBytes) {
            $this->profile(['avatar' => 'Dữ liệu gửi lên quá lớn. Vui lòng chọn ảnh không vượt quá 2 MB.'], null, 413);
            return;
        }
        if (!$this->checkFormToken()) {
            return;
        }
        $upload = new AvatarUpload();
        $path = null;
        if ($this->input('remove_avatar') !== '1') {
            try {
                $path = $upload->store(is_array($_FILES['avatar'] ?? null) ? $_FILES['avatar'] : []);
            } catch (InvalidArgumentException $exception) {
                $this->profile(['avatar' => $exception->getMessage()], null, 422);
                return;
            }
        }
        if ($path === null && $user['avatar'] === null) {
            $this->redirectAfterSave('/account/profile');
            return;
        }
        try {
            $updated = (new User())->updateAvatar((int) $user['id'], $user['avatar'], $path);
        } catch (Throwable $exception) {
            $upload->delete($path);
            throw $exception;
        }
        if (!$updated) {
            $upload->delete($path);
            $this->profile(['avatar' => 'Ảnh đại diện vừa thay đổi ở phiên khác. Vui lòng thử lại.'], null, 409);
            return;
        }
        $upload->delete($user['avatar']);
        set_flash('success', $path ? 'Đã cập nhật ảnh đại diện.' : 'Đã xóa ảnh đại diện.');
        $this->redirectAfterSave('/account/profile');
    }

    private function account(bool $withPassword = false): array
    {
        require_login();
        $id = (int) current_user()['id'];
        $model = new User();
        $user = $withPassword ? $model->findForAuthenticationById($id) : $model->findById($id);
        if ($user === null || $user['status'] !== 'active') {
            sign_out();
            require_login();
        }
        return $user;
    }

    private function input(string $key): string
    {
        $value = $_POST[$key] ?? null;
        return is_string($value) ? $value : '';
    }

    private function checkFormToken(): bool
    {
        if (verify_csrf($this->input('_csrf'))) {
            return true;
        }
        (new ErrorController())->show('Phiên biểu mẫu đã hết hạn. Vui lòng tải lại trang và thử lại.', 419);
        return false;
    }

    private function accountView(string $view, array $data, int $status): void
    {
        http_response_code($status);
        $this->view($view, $data + [
            'headExtra' => '<meta name="robots" content="noindex, nofollow">'
                . '<link rel="stylesheet" href="' . e(asset('css/pages/account.css')) . '">',
            'pageScripts' => ['js/pages/account.js'],
        ]);
    }

    private function redirectAfterSave(string $path): void
    {
        http_response_code(303);
        $this->redirect($path);
    }
}
