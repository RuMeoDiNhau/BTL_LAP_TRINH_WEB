<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/Models/User.php';

class AuthController
{
    private User $userModel;

    public function __construct(?User $userModel = null)
    {
        $this->ensureSession();
        $this->userModel = $userModel ?? new User();
    }

    public function showLogin(): void
    {
        if (!empty($_SESSION['user'])) {
            $role = $_SESSION['user']['role'] ?? 'member';
            $this->redirect($role === 'admin' ? '/admin' : '/');
            return;
        }

        $this->render('client/auth/login', [
            'pageTitle'    => 'Đăng nhập | Decor E-Commerce',
            'metaDesc'     => 'Đăng nhập tài khoản Decor E-Commerce dành cho Thành viên và Quản trị viên.',
            'topbarPrompt' => 'Chưa có tài khoản?',
            'topbarLink'   => '/register',
            'topbarText'   => 'Đăng ký ngay',
            'errors'       => [],
            'old'          => ['email' => ''],
            'csrfToken'    => $this->getCsrfToken(),
            'flashSuccess' => $this->pullFlash('success'),
            'flashError'   => $this->pullFlash('error'),
        ]);
    }

    public function login(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/login');
            return;
        }

        if (!$this->isValidCsrf($_POST['_csrf'] ?? null)) {
            $this->setFlash('error', 'Phiên biểu mẫu không hợp lệ (CSRF). Vui lòng thử lại.');
            $this->redirect('/login');
            return;
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
            $this->render('client/auth/login', [
                'pageTitle'    => 'Đăng nhập | Decor E-Commerce',
                'metaDesc'     => 'Đăng nhập tài khoản Decor E-Commerce dành cho Thành viên và Quản trị viên.',
                'topbarPrompt' => 'Chưa có tài khoản?',
                'topbarLink'   => '/register',
                'topbarText'   => 'Đăng ký ngay',
                'errors'       => $errors,
                'old'          => ['email' => $email],
                'csrfToken'    => $this->getCsrfToken(),
                'flashSuccess' => null,
                'flashError'   => 'Thông tin đăng nhập chưa hợp lệ. Vui lòng kiểm tra lại.',
            ]);
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if ($user === null || !password_verify($password, (string) $user['password_hash'])) {
            $this->render('client/auth/login', [
                'pageTitle'    => 'Đăng nhập | Decor E-Commerce',
                'metaDesc'     => 'Đăng nhập tài khoản Decor E-Commerce dành cho Thành viên và Quản trị viên.',
                'topbarPrompt' => 'Chưa có tài khoản?',
                'topbarLink'   => '/register',
                'topbarText'   => 'Đăng ký ngay',
                'errors'       => [],
                'old'          => ['email' => $email],
                'csrfToken'    => $this->getCsrfToken(),
                'flashSuccess' => null,
                'flashError'   => 'Email hoặc mật khẩu không chính xác.',
            ]);
            return;
        }

        if (($user['status'] ?? '') === 'banned') {
            $this->render('client/auth/login', [
                'pageTitle'    => 'Đăng nhập | Decor E-Commerce',
                'metaDesc'     => 'Đăng nhập tài khoản Decor E-Commerce dành cho Thành viên và Quản trị viên.',
                'topbarPrompt' => 'Chưa có tài khoản?',
                'topbarLink'   => '/register',
                'topbarText'   => 'Đăng ký ngay',
                'errors'       => [],
                'old'          => ['email' => $email],
                'csrfToken'    => $this->getCsrfToken(),
                'flashSuccess' => null,
                'flashError'   => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Quản trị viên.',
            ]);
            return;
        }

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'        => (int) $user['id'],
            'full_name' => (string) $user['full_name'],
            'email'     => (string) $user['email'],
            'role'      => (string) $user['role'],
            'avatar'    => $user['avatar'] ?? null,
        ];

        $this->setFlash('success', 'Đăng nhập thành công! Chào mừng ' . $user['full_name'] . '.');
        $this->redirect($user['role'] === 'admin' ? '/admin' : '/');
    }

    public function showRegister(): void
    {
        if (!empty($_SESSION['user'])) {
            $role = $_SESSION['user']['role'] ?? 'member';
            $this->redirect($role === 'admin' ? '/admin' : '/');
            return;
        }

        $this->render('client/auth/register', [
            'pageTitle'    => 'Đăng ký tài khoản | Decor E-Commerce',
            'metaDesc'     => 'Đăng ký tài khoản thành viên Decor E-Commerce.',
            'topbarPrompt' => 'Đã có tài khoản?',
            'topbarLink'   => '/login',
            'topbarText'   => 'Đăng nhập',
            'errors'       => [],
            'old'          => ['full_name' => '', 'email' => ''],
            'csrfToken'    => $this->getCsrfToken(),
            'flashSuccess' => $this->pullFlash('success'),
            'flashError'   => $this->pullFlash('error'),
        ]);
    }

    public function register(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/register');
            return;
        }

        if (!$this->isValidCsrf($_POST['_csrf'] ?? null)) {
            $this->setFlash('error', 'Phiên biểu mẫu không hợp lệ (CSRF). Vui lòng thử lại.');
            $this->redirect('/register');
            return;
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
            $this->render('client/auth/register', [
                'pageTitle'    => 'Đăng ký tài khoản | Decor E-Commerce',
                'metaDesc'     => 'Đăng ký tài khoản thành viên Decor E-Commerce.',
                'topbarPrompt' => 'Đã có tài khoản?',
                'topbarLink'   => '/login',
                'topbarText'   => 'Đăng nhập',
                'errors'       => $errors,
                'old'          => ['full_name' => $fullName, 'email' => $email],
                'csrfToken'    => $this->getCsrfToken(),
                'flashSuccess' => null,
                'flashError'   => 'Chưa thể tạo tài khoản. Vui lòng kiểm tra lại các thông tin bên dưới.',
            ]);
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $this->userModel->createMember($fullName, $email, $passwordHash);

        $this->setFlash('success', 'Đăng ký tài khoản Thành viên thành công! Vui lòng đăng nhập.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && !$this->isValidCsrf($_POST['_csrf'] ?? null)) {
            $this->setFlash('error', 'Yêu cầu đăng xuất không hợp lệ.');
            $this->redirect('/');
            return;
        }

        unset($_SESSION['user']);
        session_regenerate_id(true);
        $this->setFlash('success', 'Bạn đã đăng xuất thành công.');
        $this->redirect('/login');
    }

    private function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function getCsrfToken(): string
    {
        if (function_exists('csrf_token')) {
            return (string) csrf_token();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return (string) $_SESSION['csrf_token'];
    }

    private function isValidCsrf(?string $token): bool
    {
        if (function_exists('verify_csrf')) {
            return (bool) verify_csrf($token);
        }
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        return is_string($token) && $sessionToken !== '' && hash_equals($sessionToken, $token);
    }

    private function setFlash(string $type, string $message): void
    {
        if (function_exists('set_flash')) {
            set_flash($type, $message);
            return;
        }
        $_SESSION['flash'][$type] = $message;
    }

    private function pullFlash(string $type): ?string
    {
        if (function_exists('get_flash')) {
            $val = get_flash($type);
            return is_string($val) ? $val : null;
        }
        if (!isset($_SESSION['flash'][$type])) {
            return null;
        }
        $message = (string) $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }

    private function render(string $viewPath, array $data = []): void
    {
        $viewsDir = dirname(__DIR__, 2) . '/Views';
        $viewFile = $viewsDir . '/' . ltrim($viewPath, '/') . '.php';
        $layoutFile = $viewsDir . '/layouts/auth.php';

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require $layoutFile;
    }

    private function redirect(string $path): void
    {
        $url = function_exists('base_url') ? base_url($path) : $path;
        header('Location: ' . $url);
        exit;
    }
}
