<?php

declare(strict_types=1);

class AuthController extends Controller
{
    private User $userModel;

    public function __construct(?User $userModel = null)
    {
        $this->userModel = $userModel ?? new User();
    }

    public function showLogin(): void
    {
        if (is_logged_in()) {
            $this->redirectSignedInUser();
        }

        $this->renderLogin([], '', get_flash('error'), get_flash('success'));
    }

    public function login(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/login');
        }

        if (!verify_csrf($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Phiên biểu mẫu không hợp lệ (CSRF). Vui lòng thử lại.');
            $this->redirect('/login');
        }

        $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')), 'UTF-8');
        $password = (string) ($_POST['password'] ?? '');
        $errors = [];

        if ($email === '') {
            $errors['email'] = 'Vui lòng nhập địa chỉ email.';
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'Địa chỉ email không đúng định dạng.';
        }

        if ($password === '') {
            $errors['password'] = 'Vui lòng nhập mật khẩu.';
        }

        if ($errors !== []) {
            $this->renderLogin($errors, $email, 'Thông tin đăng nhập chưa hợp lệ. Vui lòng kiểm tra lại.');
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if ($user === null || !password_verify($password, (string) $user['password_hash'])) {
            $this->renderLogin([], $email, 'Email hoặc mật khẩu không chính xác.');
            return;
        }

        if (($user['status'] ?? '') === 'banned') {
            $this->renderLogin([], $email, 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Quản trị viên.');
            return;
        }

        // CHANGED: Dùng helper sign_in() để lưu luôn $_SESSION['password_version'] cho refresh_session_user()
        sign_in($user);

        set_flash('success', 'Đăng nhập thành công! Chào mừng ' . $user['full_name'] . '.');
        $this->redirectSignedInUser();
    }

    public function showRegister(): void
    {
        if (is_logged_in()) {
            $this->redirectSignedInUser();
        }

        $this->renderRegister([], ['full_name' => '', 'email' => ''], get_flash('error'), get_flash('success'));
    }

    public function register(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/register');
        }

        if (!verify_csrf($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Phiên biểu mẫu không hợp lệ (CSRF). Vui lòng thử lại.');
            $this->redirect('/register');
        }

        $fullName = trim((string) ($_POST['full_name'] ?? ''));
        $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')), 'UTF-8');
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirmation'] ?? '');
        $terms = isset($_POST['terms']);

        $errors = [];

        $nameLen = mb_strlen($fullName, 'UTF-8');
        if ($nameLen < 2 || $nameLen > 100) {
            $errors['full_name'] = 'Họ và tên phải từ 2 đến 100 ký tự.';
        }

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'Vui lòng nhập địa chỉ email hợp lệ.';
        } elseif (mb_strlen($email, 'UTF-8') > 150) {
            $errors['email'] = 'Địa chỉ email tối đa 150 ký tự.';
        } elseif ($this->userModel->emailExists($email)) {
            $errors['email'] = 'Địa chỉ email này đã được đăng ký.';
        }

        $isStrongPassword = strlen($password) >= 8
            && preg_match('/[a-z]/', $password) === 1
            && preg_match('/[A-Z]/', $password) === 1
            && preg_match('/\d/', $password) === 1;

        if (!$isStrongPassword) {
            $errors['password'] = 'Mật khẩu cần ít nhất 8 ký tự, gồm chữ hoa, chữ thường và số.';
        }

        if ($passwordConfirm === '' || $passwordConfirm !== $password) {
            $errors['password_confirmation'] = 'Mật khẩu nhập lại chưa khớp. Vui lòng nhập lại.';
        }

        if (!$terms) {
            $errors['terms'] = 'Bạn cần đồng ý với Điều khoản sử dụng và Chính sách quyền riêng tư.';
        }

        if ($errors !== []) {
            $this->renderRegister(
                $errors,
                ['full_name' => $fullName, 'email' => $email],
                'Chưa thể tạo tài khoản. Vui lòng kiểm tra lại các thông tin bên dưới.'
            );
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $this->userModel->createMember($fullName, $email, $passwordHash);

        set_flash('success', 'Đăng ký tài khoản Thành viên thành công! Vui lòng đăng nhập.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && !verify_csrf($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Yêu cầu đăng xuất không hợp lệ.');
            $this->redirect('/');
        }

        // CHANGED: Dùng helper sign_out() để dọn sạch user, password_version và csrf_token
        sign_out();
        set_flash('success', 'Bạn đã đăng xuất thành công.');
        $this->redirect('/login');
    }

    private function redirectSignedInUser(): void
    {
        $this->redirect(is_admin() ? '/admin' : '/account/profile');
    }

    private function renderLogin(array $errors, string $email, ?string $flashError = null, ?string $flashSuccess = null): void
    {
        $this->view('client/auth/login', [
            'pageTitle'    => 'Đăng nhập | Decor E-Commerce',
            'metaDesc'     => 'Đăng nhập tài khoản Decor E-Commerce dành cho Thành viên và Quản trị viên.',
            'topbarPrompt' => 'Chưa có tài khoản?',
            'topbarLink'   => '/register',
            'topbarText'   => 'Đăng ký ngay',
            'errors'       => $errors,
            'old'          => ['email' => $email],
            'flashSuccess' => $flashSuccess,
            'flashError'   => $flashError,
        ], 'layouts/auth');
    }

    private function renderRegister(array $errors, array $old, ?string $flashError = null, ?string $flashSuccess = null): void
    {
        $this->view('client/auth/register', [
            'pageTitle'    => 'Đăng ký tài khoản | Decor E-Commerce',
            'metaDesc'     => 'Đăng ký tài khoản thành viên Decor E-Commerce.',
            'topbarPrompt' => 'Đã có tài khoản?',
            'topbarLink'   => '/login',
            'topbarText'   => 'Đăng nhập',
            'errors'       => $errors,
            'old'          => $old,
            'flashSuccess' => $flashSuccess,
            'flashError'   => $flashError,
        ], 'layouts/auth');
    }
}
