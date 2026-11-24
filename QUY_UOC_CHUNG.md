# QUY ƯỚC KỸ THUẬT DÙNG CHUNG – DỰ ÁN DECOR E-COMMERCE

**Mục đích:** Thống nhất cấu trúc Session, chữ ký hàm nền tảng (`src/core/`), đường dẫn URL (Routes), quy tắc CSDL, giao diện và bảo mật giữa các module trong thư mục `src/` để toàn bộ dự án tích hợp đồng bộ và không xung đột khi ghép mã nguồn trên Git.

---

## 1. Cấu trúc `$_SESSION` dùng chung

Mọi module đọc và ghi trạng thái đăng nhập, thông báo và mã chống giả mạo form theo đúng cấu trúc sau:

```php
// 1. Thông tin người dùng đang đăng nhập (null hoặc chưa tồn tại nếu là Khách)
$_SESSION['user'] = [
    'id'        => (int) $user['id'],
    'full_name' => (string) $user['full_name'],
    'email'     => (string) $user['email'],
    'role'      => (string) $user['role'],    // 'admin' | 'member'
    'avatar'    => $user['avatar'] ?? null,   // đường dẫn ảnh hoặc null
];

// 2. Thông báo một lần sau khi chuyển trang (Flash Message)
$_SESSION['flash'] = [
    'success' => 'Thông báo thành công...',
    'error'   => 'Thông báo lỗi...',
];

// 3. Mã bảo mật chống CSRF cho mọi form POST
$_SESSION['csrf_token'] = '...chuỗi hex 64 ký tự...';
```

- Khi đăng nhập thành công hoặc đăng xuất, bắt buộc gọi `session_regenerate_id(true)`.
- Tài khoản có `status === 'banned'` không được phép đăng nhập.

---

## 2. Quy ước lớp lõi (`src/core/`)

### 2.1. Kết nối CSDL – `src/core/Database.php`
- Sử dụng mẫu Singleton `Database::getInstance()` đọc cấu hình từ `src/config/database.php` (fallback `src/config/database.example.php`).
- `Database::getInstance()` trả về đối tượng `PDO` (hoặc object có phương thức `getConnection(): PDO`) với các tùy chọn chuẩn:
  - `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`
  - `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`
  - `PDO::ATTR_EMULATE_PREPARES => false`

### 2.2. Bộ định tuyến – `src/core/Router.php`
- Hỗ trợ khai báo route theo phương thức HTTP:
  - `$router->get(string $pattern, array|callable $handler)`
  - `$router->post(string $pattern, array|callable $handler)`
- Hỗ trợ tham số động trong URL như `{id}`, `{slug}` (ví dụ: `/posts/{slug}`, `/admin/posts/{id}/edit`).
- Tự động xử lý base path khi chạy trên Apache (XAMPP thư mục con) hoặc PHP Built-in Server.
- Trả về HTTP `404` khi không khớp route nào.

### 2.3. Base Controller – `src/core/Controller.php`
- Cung cấp các phương thức dùng chung:
  - `$this->view(string $viewPath, array $data = [], ?string $layout = 'layouts/client'): void`
  - `$this->redirect(string $path): void`
  - `$this->json(array $payload, int $statusCode = 200): void`

### 2.4. Hàm tiện ích & Phân quyền – `src/core/Helpers.php`

| Tên hàm | Tham số & kiểu trả về | Chức năng |
| :--- | :--- | :--- |
| `e` | `(?string $str): string` | Escape HTML bằng `htmlspecialchars($str, ENT_QUOTES, 'UTF-8')` chống XSS |
| `base_url` | `(string $path = ''): string` | Tạo đường dẫn URL gốc chuẩn theo môi trường chạy |
| `asset` | `(string $path): string` | Tạo đường dẫn tới file tĩnh trong `src/public/assets/` |
| `csrf_token` | `(): string` | Lấy hoặc sinh mới `$_SESSION['csrf_token']` |
| `csrf_field` | `(): string` | Trả về thẻ `<input type="hidden" name="_csrf" value="...">` |
| `verify_csrf` | `(?string $token): bool` | So sánh token gửi lên bằng `hash_equals()` |
| `set_flash` | `(string $type, string $message): void` | Lưu thông báo flash (`'success'` hoặc `'error'`) |
| `get_flash` | `(string $type): ?string` | Đọc và xóa ngay thông báo flash |
| `current_user` | `(): ?array` | Trả về `$_SESSION['user']` hoặc `null` |
| `is_logged_in` | `(): bool` | Kiểm tra đã đăng nhập hay chưa |
| `is_admin` | `(): bool` | Kiểm tra `($_SESSION['user']['role'] ?? '') === 'admin'` |
| `require_login` | `(): void` | Chưa đăng nhập $\rightarrow$ lưu flash lỗi và chuyển về `/login` |
| `require_admin` | `(): void` | Chưa đăng nhập $\rightarrow$ `/login`; nếu `role !== 'admin'` $\rightarrow$ trả HTTP `403 Forbidden` |

---

## 3. Quy ước Đường dẫn URL (Routes)

Thống nhất sử dụng URL tiếng Anh, chữ thường, phân tách bằng dấu gạch ngang `-` và đăng ký tập trung tại `src/public/index.php`:

### 3.1. Nhóm Xác thực (Auth)
| Method | URL | Controller@Action | Quyền truy cập |
| :---: | :--- | :--- | :---: |
| GET | `/login` | `AuthController@showLogin` | Khách |
| POST | `/login` | `AuthController@login` | Khách |
| GET | `/register` | `AuthController@showRegister` | Khách |
| POST | `/register` | `AuthController@register` | Khách |
| POST | `/logout` | `AuthController@logout` | Đã đăng nhập |

### 3.2. Nhóm Client (Khách & Thành viên)
| Method | URL | Mô tả |
| :---: | :--- | :--- |
| GET | `/` | Trang chủ |
| GET | `/about`, `/faq`, `/contact` | Giới thiệu, Hỏi/Đáp, Liên hệ |
| GET | `/products`, `/products/{slug}` | Danh sách & Chi tiết sản phẩm |
| GET / POST | `/cart`, `/checkout`, `/orders` | Giỏ hàng, Đặt hàng, Lịch sử đơn hàng |
| GET / POST | `/account/profile`, `/account/change-password` | Hồ sơ và đổi mật khẩu của tài khoản đang đăng nhập |
| POST | `/account/avatar` | Thay hoặc xóa ảnh đại diện của tài khoản đang đăng nhập |
| GET | `/posts`, `/posts/{slug}` | Danh sách & Chi tiết bài viết / tin tức |
| POST | `/posts/{slug}/comments` | Gửi bình luận bài viết (yêu cầu đăng nhập) |

### 3.3. Nhóm Quản trị (Admin – bắt buộc `role = 'admin'`)
| Method | URL | Mô tả |
| :---: | :--- | :--- |
| GET | `/admin` | Bảng điều khiển tổng quan (`DashboardController@index`) |
| GET | `/admin/roles` | Vai trò & quyền hạn (`DashboardController@roles`) |
| GET / POST | `/admin/posts`, `/admin/posts/create`, `/admin/posts/store`, `/admin/posts/{id}/edit`, `/admin/posts/{id}/update`, `/admin/posts/{id}/delete` | Quản lý bài viết / tin tức |
| GET / POST | `/admin/comments`, `/admin/comments/{id}/status`, `/admin/comments/{id}/delete` | Quản lý bình luận bài viết |
| GET / POST | `/admin/products/*`, `/admin/categories/*`, `/admin/orders/*` | Quản lý sản phẩm, danh mục, đơn hàng |
| GET / POST | `/admin/settings/*`, `/admin/contacts/*`, `/admin/faqs/*`, `/admin/users/*` | Quản lý cấu hình, liên hệ, hỏi/đáp, người dùng |

> **Lưu ý:** Mọi thao tác làm thay đổi dữ liệu (thêm, sửa, xóa, đổi trạng thái, đăng xuất) đều ưu tiên dùng phương thức **`POST` kèm `_csrf`**, không dùng link `GET`.

---

## 4. Quy ước Cơ sở dữ liệu & Model

- **Cấu trúc bảng:** Tuân thủ đúng tên bảng và tên cột trong `src/database/schema.sql`. Nếu cần bổ sung cột/bảng mới, phải thống nhất chung trước khi chỉnh sửa `schema.sql`.
- **Dữ liệu mẫu:** Mọi câu lệnh thêm dữ liệu mẫu trong `src/database/seed.sql` đều sử dụng `INSERT IGNORE` để có thể import lại nhiều lần mà không gây lỗi trùng khóa.
- **Quy ước Model (`src/app/Models/`):**
  - Tên file và tên class viết dạng số ít, PascalCase (ví dụ: `User.php`, `Post.php`, `Comment.php`, `Product.php`).
  - Khai báo `declare(strict_types=1);` ở đầu file.
  - **100% truy vấn có tham số phải dùng PDO Prepared Statements** (`prepare()` + `bindValue()`). Tham số `LIMIT`, `OFFSET`, `id` bind kiểu `PDO::PARAM_INT`; chuỗi bind kiểu `PDO::PARAM_STR`.
  - Mọi giá trị cột `ENUM` (`role`, `status`) phải được kiểm tra theo danh sách cho phép (whitelist) trước khi lưu hoặc lọc.

---

## 5. Quy ước Views, Layouts & Assets

- **Hệ thống Layout trong `src/app/Views/layouts/`:**
  - `client.php`: Layout chung cho các trang phía khách (Header, Navbar, Footer).
  - `auth.php`: Layout 2 cột cho trang Đăng nhập / Đăng ký.
  - `admin.php`: Layout quản trị dùng chung (TailAdmin – Sidebar, Topbar, Flash message).
- **Biến truyền vào Layout:**
  - SEO cơ bản: `$pageTitle`, `$metaDesc`, `$metaKeywords`.
  - Trạng thái menu Admin: `$activeMenu`, `$breadcrumb`, `$pageScripts` (mảng đường dẫn file JS riêng từng trang).
- **Tài nguyên tĩnh & Upload:**
  - CSS/JS/hình ảnh giao diện đặt trong `src/public/assets/` (`css/`, `js/`, `images/`, `vendor/`). Tách riêng CSS phía khách (`style.css`, `auth.css`) và phía quản trị (`admin.css`) để tránh xung đột style.
  - Hình ảnh người dùng/admin upload bắt buộc lưu trực tiếp trên server trong `src/public/uploads/` và chia thư mục con theo module (`uploads/posts/`, `uploads/products/`, `uploads/avatars/`, `uploads/banners/`). Không sử dụng URL ảnh từ trang web bên ngoài.

---

## 6. Quy chuẩn Bảo mật, Validation & Phân trang

- **Chống XSS:** Mọi dữ liệu người dùng nhập vào khi in ra HTML đều phải bọc qua `e(...)` (`htmlspecialchars`). Nội dung HTML từ trình soạn thảo WYSIWYG phải được lọc thẻ/thuộc tính nguy hiểm (`<script>`, `on*=`, `javascript:`) trước khi lưu.
- **Chống CSRF:** Mọi `<form method="post">` đều có `<input type="hidden" name="_csrf" value="...">` và được kiểm tra ở phía Server trước khi xử lý.
- **Bảo mật mật khẩu:** Luôn băm bằng `password_hash($password, PASSWORD_BCRYPT)` và kiểm tra bằng `password_verify()`. Không lưu mật khẩu dạng văn bản thường.
- **Validation 2 lớp:** Mọi form đều kiểm tra dữ liệu đầu vào ở cả **Client-side (JavaScript)** và **Server-side (PHP)**. Khi có lỗi phía Server, giữ lại dữ liệu người dùng đã nhập (`$old`) và hiển thị lỗi rõ ràng dưới từng trường.
- **Phân trang (Pagination):** Tất cả các trang danh sách dài và trang quản trị đều hỗ trợ phân trang, giữ nguyên từ khóa tìm kiếm và bộ lọc trên URL khi chuyển trang (ví dụ: `?keyword=sofa&status=published&page=2`).
- **Chuẩn HTML5/CSS3 & Responsive:** Kiểm tra mã HTML/CSS qua W3C Validator và đảm bảo hiển thị tốt trên Mobile, Tablet, Desktop.

---

## 7. Quy ước làm việc với Git

- **Không push trực tiếp lên nhánh `main`.** Mỗi tính năng phát triển trên một nhánh riêng theo cú pháp `feature/<ten-tinh-nang>` và tạo Pull Request để gộp vào `main`.
- **Không commit các file nhạy cảm / file cục bộ:**
  - `src/config/database.php` (chỉ commit file mẫu `src/config/database.example.php`).
  - Các file ảnh trong `src/public/uploads/*` (chỉ giữ lại `.gitkeep`).
- **Hạn chế sửa chéo file dùng chung:** Mỗi module tách riêng Controller, Model, thư mục View và file CSS/JS riêng để giảm tối đa xung đột khi merge.
