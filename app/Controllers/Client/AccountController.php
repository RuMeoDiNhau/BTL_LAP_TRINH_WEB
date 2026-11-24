<?php

declare(strict_types=1);

class AccountController extends Controller
{
    public function profile(array $errors = [], ?array $old = null, int $status = 200): void
    {
        $user = $this->requireUser();
        $this->render('client/account/profile', [
            'title' => 'Hồ sơ cá nhân', 'active' => 'profile', 'user' => $user,
            'errors' => $errors, 'old' => $old ?? $user,
        ], $status);
    }

    public function updateProfile(): void
    {
        $user = $this->requireUser();
        verify_csrf();
        $data = [
            'full_name' => trim(input('full_name')), 'email' => strtolower(trim(input('email'))),
            'phone' => trim(input('phone')),
        ];
        $errors = AccountValidation::profile($data);
        $model = new User();
        if (!isset($errors['email']) && $model->emailExists($data['email'], (int) $user['id'])) {
            $errors['email'] = 'Email này đã được tài khoản khác sử dụng.';
        }
        // Email là thông tin đăng nhập: yêu cầu mật khẩu hiện tại khi thay đổi.
        if ($data['email'] !== strtolower($user['email']) && !password_verify(input('email_password'), $user['password_hash'])) {
            $errors['email_password'] = 'Nhập đúng mật khẩu hiện tại để đổi email đăng nhập.';
        }
        if ($errors) {
            $this->profile($errors, $data, 422);
            return;
        }
        try {
            $model->updateProfile((int) $user['id'], $data);
        } catch (PDOException $exception) {
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }
            $this->profile(['email' => 'Email này đã được tài khoản khác sử dụng.'], $data, 422);
            return;
        }
        flash('Đã lưu thông tin cá nhân của bạn.');
        $this->redirect('/account/profile');
    }

    public function password(array $errors = [], int $status = 200): void
    {
        $this->render('client/account/change-password', [
            'title' => 'Đổi mật khẩu', 'active' => 'password',
            'user' => $this->requireUser(), 'errors' => $errors,
        ], $status);
    }

    public function updatePassword(): void
    {
        $user = $this->requireUser();
        verify_csrf();
        $password = input('password');
        $errors = AccountValidation::password($password, input('password_confirmation'));
        if (!password_verify(input('current_password'), $user['password_hash'])) {
            $errors['current_password'] = 'Mật khẩu hiện tại không chính xác.';
        }
        if (password_verify($password, $user['password_hash'])) {
            $errors['password'] = 'Mật khẩu mới phải khác mật khẩu hiện tại.';
        }
        if ($errors) {
            $this->password($errors, 422);
            return;
        }
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        if (!(new User())->updatePassword((int) $user['id'], $user['password_hash'], $newHash)) {
            sign_out();
            flash('Mật khẩu đã thay đổi ở một phiên khác. Vui lòng đăng nhập lại.');
            $this->redirect('/login');
        }
        $user['password_hash'] = $newHash;
        sign_in($user);
        flash('Đã đổi mật khẩu. Các phiên đăng nhập khác sẽ cần đăng nhập lại.');
        $this->redirect('/account/change-password');
    }

    public function updateAvatar(): void
    {
        $user = $this->requireUser();
        verify_csrf();
        $upload = new AvatarUpload();
        $path = null;
        if (input('remove_avatar') !== '1') {
            try {
                $path = $upload->store(is_array($_FILES['avatar'] ?? null) ? $_FILES['avatar'] : []);
            } catch (InvalidArgumentException $exception) {
                $this->profile(['avatar' => $exception->getMessage()], null, 422);
                return;
            }
        }
        try {
            (new User())->updateAvatar((int) $user['id'], $path);
        } catch (Throwable $exception) {
            $upload->delete($path);
            throw $exception;
        }
        $upload->delete($user['avatar']);
        flash($path ? 'Đã cập nhật ảnh đại diện.' : 'Đã xóa ảnh đại diện.');
        $this->redirect('/account/profile');
    }
}
