<?php

declare(strict_types=1);

/**
 * Kiểm thử HTTP + MySQL thật. Chỉ chạy trên database có tên decor_account_test*.
 * PHP cần curl, pdo_mysql. Không chạy trên database decor_db của nhóm.
 * Ví dụ biến môi trường và cách chạy nằm trong README, mục Tài khoản cá nhân.
 */
$base = rtrim(getenv('DECOR_TEST_URL') ?: 'http://localhost:8017', '/');
$name = getenv('DECOR_DB_DBNAME') ?: '';
if (!preg_match('/^decor_account_test[a-z0-9_]*$/D', $name)) {
    exit("Từ chối chạy: DECOR_DB_DBNAME phải bắt đầu bằng decor_account_test.\n");
}
$db = new PDO('mysql:host=' . (getenv('DECOR_DB_HOST') ?: '127.0.0.1') . ';port=' . (getenv('DECOR_DB_PORT') ?: '3306') . ';charset=utf8mb4', getenv('DECOR_DB_USERNAME') ?: 'root', getenv('DECOR_DB_PASSWORD') ?: '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
foreach (['schema.sql', 'seed.sql'] as $file) {
    $db->exec(str_replace('`decor_db`', '`' . $name . '`', file_get_contents(__DIR__ . '/../database/' . $file)));
}

final class Browser
{
    private string $cookieFile;
    public function __construct()
    {
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'decor-cookie-');
    }
    public function request(string $route, ?array $data = null): array
    {
        global $base;
        $ch = curl_init($base . $route);
        $headers = [];
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEJAR => $this->cookieFile,
            CURLOPT_COOKIEFILE => $this->cookieFile, CURLOPT_TIMEOUT => 30, CURLOPT_HTTPHEADER => ['Expect:'],
            CURLOPT_HEADERFUNCTION => static function ($ch, $line) use (&$headers) { $headers[] = $line; return strlen($line); }]);
        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $body = curl_exec($ch);
        if ($body === false) throw new RuntimeException(curl_error($ch));
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        return ['status' => $status, 'body' => $body, 'headers' => implode('', $headers)];
    }
    public function token(string $route): string
    {
        $page = $this->request($route);
        if (!preg_match('/name="_csrf" value="([a-f0-9]{64})"/', $page['body'], $match)) throw new RuntimeException('Không tìm thấy CSRF token: ' . $route . ' HTTP ' . $page['status']);
        return $match[1];
    }
    public function post(string $route, array $data): array
    {
        $page = str_starts_with($route, '/account/') || $route === '/logout' ? '/account/profile' : $route;
        return $this->request($route, ['_csrf' => $this->token($page)] + $data);
    }
    public function __destruct()
    {
        if (is_file($this->cookieFile)) unlink($this->cookieFile);
    }
}
$checks = 0;
function check(bool $condition, string $description): void
{
    global $checks;
    if (!$condition) throw new RuntimeException('FAIL: ' . $description);
    echo 'PASS: ' . $description . "\n";
    $checks++;
}
function row(int $id): array
{
    global $db;
    $query = $db->prepare('SELECT * FROM users WHERE id = ?');
    $query->execute([$id]);
    return $query->fetch();
}
function png(int $width, int $height): string
{
    $chunk = static fn ($type, $data) => pack('N', strlen($data)) . $type . $data . pack('N', crc32($type . $data));
    return "\x89PNG\r\n\x1a\n" . $chunk('IHDR', pack('NNCCCCC', $width, $height, 8, 2, 0, 0, 0)) . $chunk('IDAT', gzcompress("\0\x59\x67\x45")) . $chunk('IEND', '');
}

$files = [];
$ids = [];
$uploaded = [];
try {
    $guest = new Browser();
    $handshake = $guest->request('/register');
    check(str_contains($handshake['headers'], 'X-Decor-Test-Database: ' . $name . "\r\n"), 'Máy chủ HTTP dùng đúng database thử, không phải dữ liệu thật');
    check(str_contains($guest->request('/register', [])['headers'], 'Location: /register'), 'Đăng ký yêu cầu CSRF');
    check(str_contains($guest->request('/login', [])['headers'], 'Location: /login'), 'Đăng nhập yêu cầu CSRF');
    check(str_contains($guest->request('/account/profile')['headers'], 'Location: /login'), 'Khách được chuyển đến đăng nhập');
    check(str_contains($guest->request('/admin/roles')['headers'], 'Location: /login'), 'Khách chưa vào được trang quản trị');
    check($guest->request('/logout')['status'] === 404, 'Đăng xuất không chấp nhận GET');
    check($guest->request('/does-not-exist')['status'] === 404, 'Route không tồn tại trả 404');
    $browser = new Browser();
    $email = 'account-' . bin2hex(random_bytes(5)) . '@example.com';
    $password = 'Decor@2026';
    $result = $browser->post('/register', ['full_name' => 'Nguyễn An', 'email' => $email, 'password' => $password, 'password_confirmation' => $password, 'terms' => '1', 'role' => 'admin', 'status' => 'banned']);
    check(str_contains($result['headers'], 'Location: /login'), 'Đăng ký chuyển về trang đăng nhập của nhóm');
    $query = $db->prepare('SELECT id FROM users WHERE email = ?'); $query->execute([$email]);
    $id = (int) $query->fetchColumn(); $ids[] = $id;
    check(row($id)['role'] === 'member' && row($id)['status'] === 'active', 'Không thể tự cấp quyền admin khi đăng ký');
    check(password_verify($password, row($id)['password_hash']), 'Mật khẩu được lưu bằng hash');
    $login = $browser->post('/login', ['email' => $email, 'password' => $password]);
    check(str_contains($login['headers'], "Location: /\r\n"), 'Auth của nhóm chuyển thành viên tới trang chủ');
    $profile = $browser->request('/account/profile');
    check($profile['status'] === 200 && str_contains($profile['body'], 'Nguyễn An'), 'Đọc hồ sơ sau khi đăng ký');
    check($browser->request('/admin/roles')['status'] === 403, 'Thành viên không vào được trang quản trị');
    $dom = new DOMDocument(); libxml_use_internal_errors(true); $dom->loadHTML($profile['body']); libxml_clear_errors();
    $idsInPage = [];
    foreach ($dom->getElementsByTagName('*') as $element) {
        if ($element->hasAttribute('id')) $idsInPage[] = $element->getAttribute('id');
    }
    check(count($idsInPage) === count(array_unique($idsInPage)), 'HTML hồ sơ không có ID trùng');
    foreach ($dom->getElementsByTagName('label') as $label) {
        if ($label->hasAttribute('for')) check(in_array($label->getAttribute('for'), $idsInPage, true), 'Nhãn form nối đúng trường: ' . $label->getAttribute('for'));
    }
    foreach (['/assets/css/pages/account.css', '/assets/js/pages/account.js'] as $asset) {
        $ch = curl_init($base . $asset); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); $content = curl_exec($ch); $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE); curl_close($ch);
        check($status === 200 && strlen($content) > 100, 'Tải được tài nguyên giao diện: ' . $asset);
    }
    check(str_contains($profile['headers'], 'HttpOnly') || !str_contains($profile['headers'], 'Set-Cookie'), 'Phiên không xuất cookie có thể đọc bằng JavaScript');
    $safeName = '<b>Nguyễn An</b>';
    $result = $browser->post('/account/profile', ['full_name' => $safeName, 'email' => $email, 'phone' => '0912345678', 'id' => '1', 'user_id' => '1', 'role' => 'admin', 'status' => 'banned']);
    check($result['status'] === 303 && row($id)['full_name'] === $safeName && row($id)['phone'] === '0912345678', 'Lưu thông tin cá nhân vào MySQL');
    check(row($id)['role'] === 'member' && row(1)['full_name'] === 'Quản trị viên', 'Bỏ qua ID và vai trò giả mạo, chỉ sửa tài khoản hiện tại');
    $profile = $browser->request('/account/profile');
    check(str_contains($profile['body'], '&lt;b&gt;Nguyễn An&lt;/b&gt;') && !str_contains($profile['body'], $safeName), 'Escape dữ liệu hồ sơ trước khi hiển thị');
    foreach (['/account/profile', '/account/avatar', '/account/change-password'] as $route) {
        check($browser->request($route, [])['status'] === 419, 'Chặn CSRF tại ' . $route);
    }
    $browser->request('/logout', []);
    check($browser->request('/account/profile')['status'] === 200, 'Đăng xuất thiếu CSRF giữ nguyên phiên');
    $data = ['full_name' => 'Nguyễn An', 'email' => $email, 'phone' => 'abc'];
    check($browser->post('/account/profile', $data)['status'] === 422 && row($id)['phone'] === '0912345678', 'Số điện thoại sai không thay đổi dữ liệu');
    $data['phone'] = '';
    $data['email'] = 'admin@decor.vn'; $data['email_password'] = $password;
    check($browser->post('/account/profile', $data)['status'] === 422 && row($id)['email'] === $email, 'Email trùng bị từ chối');
    $newEmail = 'updated-' . $email; $data['email'] = $newEmail; $data['email_password'] = 'wrong';
    check($browser->post('/account/profile', $data)['status'] === 422 && row($id)['email'] === $email, 'Đổi email yêu cầu đúng mật khẩu hiện tại');
    $data['email_password'] = $password;
    check($browser->post('/account/profile', $data)['status'] === 303 && row($id)['email'] === $newEmail && row($id)['phone'] === null, 'Đổi email thành công và cho phép bỏ trống điện thoại');

    foreach ([['fake.jpg', 'image/jpeg', 'this is not an image'], ['script.svg', 'image/svg+xml', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'], ['large.png', 'image/png', png(1, 1) . str_repeat('x', 2097152)], ['wide.png', 'image/png', png(4097, 1)]] as [$filename, $mime, $content]) {
        $tmp = tempnam(sys_get_temp_dir(), 'decor-image-'); file_put_contents($tmp, $content); $files[] = $tmp;
        $result = $browser->post('/account/avatar', ['avatar' => new CURLFile($tmp, $mime, $filename)]);
        check($result['status'] === 422 && row($id)['avatar'] === null, 'Từ chối avatar không hợp lệ: ' . $filename);
    }
    preg_match('/X-Decor-Test-Post-Max-Size: ([^\r\n]+)/i', $handshake['headers'], $limitMatch);
    $limit = trim($limitMatch[1] ?? '0');
    $limitBytes = (int) ((float) $limit * match (strtolower(substr($limit, -1))) { 'g' => 1073741824, 'm' => 1048576, 'k' => 1024, default => 1 });
    if ($limitBytes > 0 && $limitBytes <= 64 * 1024 * 1024) {
        $tmp = tempnam(sys_get_temp_dir(), 'decor-image-'); $files[] = $tmp; $handle = fopen($tmp, 'wb');
        for ($remaining = $limitBytes + 1024; $remaining > 0; $remaining -= $written) $written = fwrite($handle, str_repeat('x', min($remaining, 1048576)));
        fclose($handle);
        check($browser->post('/account/avatar', ['avatar' => new CURLFile($tmp, 'image/png', 'oversized.png')])['status'] === 413 && row($id)['avatar'] === null, 'Body vượt post_max_size trả lỗi dung lượng, không báo nhầm CSRF');
    }
    $tmp = tempnam(sys_get_temp_dir(), 'decor-image-'); file_put_contents($tmp, png(1, 1)); $files[] = $tmp;
    $result = $browser->post('/account/avatar', ['avatar' => new CURLFile($tmp, 'image/png', 'anh.php.png')]);
    $avatar = row($id)['avatar']; $uploaded[] = $avatar;
    check($result['status'] === 303 && (bool) preg_match('~^uploads/avatars/[a-f0-9]{32}\.png$~D', $avatar), 'Upload ảnh thật với tên ngẫu nhiên an toàn');
    $image = curl_init($base . '/' . $avatar); curl_setopt($image, CURLOPT_RETURNTRANSFER, true); curl_exec($image); check(curl_getinfo($image, CURLINFO_RESPONSE_CODE) === 200, 'Ảnh được phục vụ từ server'); curl_close($image);
    $browser->post('/account/avatar', ['avatar' => new CURLFile($tmp, 'image/png', 'avatar.png')]);
    $replacement = row($id)['avatar']; $uploaded[] = $replacement;
    check($replacement !== $avatar && !is_file(__DIR__ . '/../public/' . $avatar), 'Thay ảnh và dọn file avatar cũ');
    check($browser->post('/account/avatar', ['remove_avatar' => '1'])['status'] === 303 && row($id)['avatar'] === null && !is_file(__DIR__ . '/../public/' . $replacement), 'Xóa avatar và dùng lại ảnh mặc định');

    $other = new Browser();
    $other->post('/login', ['email' => $newEmail, 'password' => $password]);
    check($other->request('/account/profile')['status'] === 200, 'Đăng nhập phiên thứ hai bằng email mới');
    foreach ([['wrong', 'NewDecor@2026', 'NewDecor@2026'], [$password, 'weak', 'weak'], [$password, 'ÁÁA1aa', 'ÁÁA1aa'], [$password, str_repeat('a', 70) . 'A1x', str_repeat('a', 70) . 'A1x'], [$password, $password, $password], [$password, 'NewDecor@2026', 'Mismatch@2026']] as [$current, $next, $confirmation]) {
        $result = $browser->post('/account/change-password', ['current_password' => $current, 'password' => $next, 'password_confirmation' => $confirmation]);
        check($result['status'] === 422 && password_verify($password, row($id)['password_hash']), 'Từ chối đổi mật khẩu với dữ liệu sai');
        check(!str_contains($result['body'], 'value="' . $next . '"'), 'Không đưa mật khẩu đã nhập trở lại HTML');
    }
    $newPassword = 'NewDecor@2026';
    check($browser->post('/account/change-password', ['current_password' => $password, 'password' => $newPassword, 'password_confirmation' => $newPassword])['status'] === 303 && password_verify($newPassword, row($id)['password_hash']), 'Đổi mật khẩu và lưu hash mới');
    check($browser->request('/account/profile')['status'] === 200, 'Phiên hiện tại vẫn sử dụng được sau đổi mật khẩu');
    check(str_contains($other->request('/account/profile')['headers'], 'Location: /login'), 'Phiên cũ bị thu hồi sau đổi mật khẩu');
    $fresh = new Browser();
    $fresh->post('/login', ['email' => $newEmail, 'password' => $password]);
    check(str_contains($fresh->request('/account/profile')['headers'], 'Location: /login'), 'Mật khẩu cũ không đăng nhập được');
    $fresh->post('/login', ['email' => $newEmail, 'password' => $newPassword]);
    check($fresh->request('/account/profile')['status'] === 200, 'Mật khẩu mới đăng nhập được');
    $adminResetPassword = 'ResetDecor@2026';
    $db->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([password_hash($adminResetPassword, PASSWORD_BCRYPT), $id]);
    check(str_contains($fresh->request('/account/profile')['headers'], 'Location: /login'), 'Đổi hash qua luồng reset của quản trị sẽ thu hồi phiên');
    $fresh->post('/login', ['email' => $newEmail, 'password' => $adminResetPassword]);
    check($fresh->request('/account/profile')['status'] === 200, 'Mật khẩu sau reset đăng nhập được');
    $db->prepare("UPDATE users SET status = 'banned' WHERE id = ?")->execute([$id]);
    check(str_contains($fresh->request('/account/profile')['headers'], 'Location: /login'), 'Tài khoản bị khóa mất quyền truy cập hồ sơ');
    $fresh->post('/login', ['email' => $newEmail, 'password' => $adminResetPassword]);
    check(str_contains($fresh->request('/account/profile')['headers'], 'Location: /login'), 'Tài khoản bị khóa không đăng nhập được');
    $admin = new Browser();
    $login = $admin->post('/login', ['email' => 'admin@decor.vn', 'password' => 'admin123']);
    check(str_contains($login['headers'], 'Location: /admin'), 'Auth của nhóm chuyển quản trị viên tới dashboard');
    check($admin->request('/admin')['status'] === 200 && $admin->request('/admin/roles')['status'] === 200, 'Dashboard và phân quyền của nhóm vẫn hoạt động');
    check($admin->request('/account/profile')['status'] === 200, 'Admin cũng có thể quản lý hồ sơ của chính mình');
    $admin->post('/logout', []);
    check(str_contains($admin->request('/account/profile')['headers'], 'Location: /login'), 'Đăng xuất hủy quyền truy cập hồ sơ');
    echo "\nĐã vượt qua $checks kiểm tra.\n";
} finally {
    foreach ($uploaded as $path) {
        if ($path && preg_match('~^uploads/avatars/[a-f0-9]{32}\.png$~D', $path) && is_file(__DIR__ . '/../public/' . $path)) unlink(__DIR__ . '/../public/' . $path);
    }
    foreach ($files as $file) if (is_file($file)) unlink($file);
    foreach ($ids as $id) $db->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
}
