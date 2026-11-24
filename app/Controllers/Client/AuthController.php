<?php

declare(strict_types=1);

/** Luồng xác thực PHP tối thiểu để tính năng tài khoản hoạt động độc lập. */
class AuthController extends Controller
{
    public function login(array $errors = [], array $old = [], int $status = 200): void
    {
        if (current_user()) {
            $this->redirect('/account/profile');
        }
        $this->render('client/auth/login', ['title' => 'Đăng nhập', 'user' => null, 'errors' => $errors, 'old' => $old], $status);
    }

    public function authenticate(): void
    {
        verify_csrf();
        $email = strtolower(trim(input('email')));
        $password = input('password');
        // Giới hạn thử liên tiếp trong phiên để tránh gửi dồn từ cùng trình duyệt.
        $attempts = array_filter($_SESSION['login_attempts'] ?? [], fn ($at) => $at > time() - 300);
        if (count($attempts) >= 10) {
            $this->login(['form' => 'Bạn đã thử nhiều lần. Vui lòng chờ 5 phút rồi thử lại.'], ['email' => $email], 429);
            return;
        }
        $attempts[] = time();
        $_SESSION['login_attempts'] = $attempts;
        $user = filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) <= 4096 ? (new User())->findByEmail($email) : null;
        if (!$user || !password_verify($password, $user['password_hash']) || $user['status'] !== 'active') {
            $this->login(['form' => 'Email hoặc mật khẩu không đúng, hoặc tài khoản đã bị khóa.'], ['email' => $email], 422);
            return;
        }
        unset($_SESSION['login_attempts']);
        sign_in($user);
        flash('Đăng nhập thành công. Chào mừng bạn trở lại Decor.');
        $this->redirect('/account/profile');
    }

    public function register(array $errors = [], array $old = [], int $status = 200): void
    {
        if (current_user()) {
            $this->redirect('/account/profile');
        }
        $this->render('client/auth/register', ['title' => 'Đăng ký', 'user' => null, 'errors' => $errors, 'old' => $old], $status);
    }

    public function store(): void
    {
        verify_csrf();
        $data = ['full_name' => trim(input('full_name')), 'email' => strtolower(trim(input('email'))), 'phone' => '', 'password' => input('password')];
        $errors = AccountValidation::profile($data) + AccountValidation::password($data['password'], input('password_confirmation'));
        $model = new User();
        if (!isset($errors['email']) && $model->emailExists($data['email'])) {
            $errors['email'] = 'Email này đã được đăng ký.';
        }
        $old = ['full_name' => $data['full_name'], 'email' => $data['email']];
        if ($errors) {
            $this->register($errors, $old, 422);
            return;
        }
        try {
            $id = $model->create($data);
        } catch (PDOException $exception) {
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }
            $this->register(['email' => 'Email này đã được đăng ký.'], $old, 422);
            return;
        }
        sign_in($model->findById($id));
        flash('Tạo tài khoản thành công. Bạn có thể cập nhật hồ sơ ngay tại đây.');
        $this->redirect('/account/profile');
    }

    public function logout(): void
    {
        verify_csrf();
        sign_out();
        flash('Bạn đã đăng xuất.');
        $this->redirect('/login');
    }
}
