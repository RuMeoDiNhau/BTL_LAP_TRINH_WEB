# DECOR E-COMMERCE

Website Thương mại Điện tử / Doanh nghiệp Đồ trang trí & Nội thất.

- **Kiến trúc:** MVC tự viết bằng **PHP thuần** (không dùng framework/CMS).
- **Cơ sở dữ liệu:** MySQL 8.0 hoặc MariaDB 10.4+.
- **Frontend:** HTML / CSS / JavaScript thuần.
- **Zero dependency:** không Composer, không npm, không ORM, không template engine.

---

## 1. Trạng thái dự án

### Đã hoàn thành

- `database/schema.sql` — tạo CSDL `decor_db` và **14 bảng** quan hệ đầy đủ khóa ngoại.
- `database/seed.sql` — dữ liệu mẫu: 2 tài khoản, 3 danh mục, 4 cấu hình.
- `config/database.php` — cấu hình PDO cho MariaDB/MySQL (file local, không commit).
- Đã kiểm thử thực tế trên **XAMPP MariaDB 10.4.32 + PHP 8.2.12**:
  - Kết nối PDO thành công tới `decor_db`.
  - Đủ 14 bảng, 2 user.
  - Hash bcrypt của `admin123` và `123456` kiểm tra bằng `password_verify()` đều đúng.

### Chưa triển khai

Toàn bộ mã nguồn ứng dụng vẫn đang là **file rỗng (placeholder)**:

- `core/Database.php`, `core/Router.php`, `core/Controller.php`, `core/Helpers.php`
- Toàn bộ `app/Controllers/`, `app/Models/`, `app/Views/`
- `public/index.php`, `public/.htaccess`
- `public/assets/css/*`, `public/assets/js/*`

→ Hiện tại **chưa có giao diện hoặc route để chạy**. Cần implement tiếp theo roadmap ở mục 7.

---

## 2. Yêu cầu môi trường

| Thành phần | Yêu cầu |
| :--- | :--- |
| PHP | `>= 8.0` (khuyến nghị 8.2.x) |
| Extension | `pdo_mysql` (bật sẵn trong XAMPP) |
| CSDL | MariaDB 10.4+ hoặc MySQL 8.0+ |
| Web server | PHP Built-in Server hoặc Apache (XAMPP/Laragon/WAMP) |
| Hệ điều hành | Windows (hướng dẫn bên dưới), Linux/macOS vẫn chạy được |

> Dự án đã được kiểm thử với **XAMPP tại `E:\xampp`**. Nếu dùng XAMPP, PHP trong PATH thường là `E:\xampp\php\php.exe`.

---

## 3. Hướng dẫn cài đặt & khởi chạy

### Bước 1: Khởi động MariaDB/MySQL

**Cách 1 — XAMPP (khuyến nghị):**

1. Mở `E:\xampp\xampp-control.exe`.
2. Bấm **Start** ở dòng **MySQL**.
3. Kiểm tra port `3306` đã mở.

**Cách 2 — MySQL 8.0 cài như service Windows:**

```powershell
Start-Service MySQL80
```

> ⚠️ Không chạy đồng thời XAMPP MariaDB và MySQL80 nếu cả hai cùng dùng port `3306`.

Kiểm tra kết nối:

```bat
E:\xampp\mysql\bin\mysql.exe -u root -p -e "SELECT VERSION();"
```

### Bước 2: Import cơ sở dữ liệu

Import **lần lượt** 2 file (không cần tạo database trước):

1. `database/schema.sql` — tạo CSDL `decor_db` (charset `utf8mb4`, collation `utf8mb4_unicode_ci`) và 14 bảng.
2. `database/seed.sql` — thêm dữ liệu mẫu.

**Bằng phpMyAdmin:** mở [http://localhost/phpmyadmin](http://localhost/phpmyadmin) → **Import** → chọn từng file theo thứ tự.

**Bằng MySQL CLI (CMD):**

```bat
cd /d D:\Downloads\HK261\Web\Assignment\BTL_LAP_TRINH_WEB

E:\xampp\mysql\bin\mysql.exe -u root -p < database\schema.sql
E:\xampp\mysql\bin\mysql.exe -u root -p < database\seed.sql
```

> PowerShell không hỗ trợ toán tử `<`. Hãy dùng **CMD** hoặc phpMyAdmin.

Đặc điểm của 2 file:

- `schema.sql` dùng `CREATE TABLE IF NOT EXISTS` → **không xóa dữ liệu** đang có.
- `seed.sql` dùng `INSERT IGNORE` → chạy lại nhiều lần không lỗi trùng khóa, không ghi đè dữ liệu đã sửa.

### Bước 3: Cấu hình kết nối CSDL

Trên máy hiện tại, `config/database.php` đã được tạo và kết nối thành công.

Khi clone dự án sang máy khác:

```powershell
Copy-Item config\database.example.php config\database.php
```

Sau đó mở `config/database.php` và điền thông tin thật:

```php
<?php
return [
    'host'     => '127.0.0.1',
    'port'     => '3306',
    'dbname'   => 'decor_db',
    'username' => 'root',
    'password' => 'mật_khẩu_CSDL_của_bạn',
    'charset'  => 'utf8mb4',
];
```

> `config/database.php` đã được đưa vào `.gitignore` vì chứa mật khẩu. Chỉ commit file mẫu `config/database.example.php`.

### Bước 4: Khởi chạy ứng dụng

Sau khi đã triển khai `public/index.php` và `core/Router.php`:

```bash
php -S localhost:8000 -t public
```

Truy cập:

- **Client:** [http://localhost:8000](http://localhost:8000)
- **Admin:** [http://localhost:8000/admin](http://localhost:8000/admin)

> Ở trạng thái hiện tại, `public/index.php` còn rỗng nên trình duyệt sẽ chỉ trả về trang trắng.

---

## 4. Tài khoản đăng nhập kiểm thử

Dữ liệu mẫu từ `database/seed.sql`:

| Vai trò | Email đăng nhập | Mật khẩu mặc định |
| :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@decor.vn` | `admin123` |
| **Khách hàng (Customer)** | `customer@decor.vn` | `123456` |

Mật khẩu được lưu dưới dạng hash **bcrypt** (`password_hash`), không lưu plain text.

---

## 5. Cấu trúc thư mục

```text
├── app/
│   ├── Controllers/
│   │   ├── Client/             # Controllers phía người dùng
│   │   └── Admin/              # Controllers phía quản trị
│   ├── Models/                 # Model tương tác CSDL qua PDO
│   └── Views/
│       ├── layouts/            # Layout dùng chung (client.php, admin.php)
│       ├── client/             # Giao diện người dùng
│       └── admin/              # Giao diện quản trị
├── config/
│   ├── database.php            # Cấu hình thật (bị gitignore)
│   └── database.example.php    # File mẫu để copy
├── core/
│   ├── Controller.php          # Base Controller (render view, json, redirect)
│   ├── Database.php            # PDO Singleton connection
│   ├── Router.php              # Bộ điều hướng URL
│   └── Helpers.php             # Hàm tiện ích dùng chung
├── database/
│   ├── schema.sql              # Cấu trúc CSDL: 14 bảng quan hệ
│   └── seed.sql                # Dữ liệu mẫu
├── public/
│   ├── index.php               # Front controller (điểm nhận request)
│   ├── .htaccess               # URL Rewrite cho Apache
│   ├── assets/                 # CSS, JS, fonts, images
│   └── uploads/                # Ảnh upload (bị gitignore, giữ .gitkeep)
├── .gitignore
└── README.md
```

---

## 6. Cơ sở dữ liệu

14 bảng trong `database/schema.sql`:

| Nhóm | Bảng |
| :--- | :--- |
| Người dùng & cấu hình | `users`, `settings` |
| Trang nội dung | `page_contents`, `contacts`, `faqs` |
| Sản phẩm | `categories`, `products`, `product_images`, `reviews` |
| Giỏ hàng & đơn hàng | `cart_items`, `orders`, `order_items` |
| Tin tức | `posts`, `comments` |

Quan hệ chính:

- `products.category_id` → `categories.id` (`ON DELETE RESTRICT`)
- `categories.parent_id` → `categories.id` (`ON DELETE SET NULL`)
- `orders.user_id` → `users.id` (`ON DELETE RESTRICT`)
- `order_items.order_id` → `orders.id` (`ON DELETE CASCADE`)
- `order_items.product_id` → `products.id` (`ON DELETE RESTRICT`)
- `reviews`, `cart_items`, `comments`, `product_images` → `ON DELETE CASCADE` theo bảng cha tương ứng.

### Reset cơ sở dữ liệu

```sql
DROP DATABASE decor_db;
```

Sau đó import lại `database/schema.sql` và `database/seed.sql`.

---

## 7. Việc cần làm tiếp

1. `core/Database.php` — PDO Singleton, đọc cấu hình từ `config/database.php`.
2. `core/Router.php` + `public/index.php` + `.htaccess` — routing cơ bản.
3. `core/Controller.php`, `core/Helpers.php` — render view, redirect, escape HTML.
4. Client: Home, Product, Cart, Auth, Post, Page (About/Contact/FAQ).
5. Admin: Dashboard, Product, Order, Post, User, Setting.
6. Views + CSS/JS cho client và admin.
7. Kiểm thử luồng đăng nhập, giỏ hàng, đặt hàng, quản trị.

---

## 8. Xử lý sự cố

| Lỗi | Nguyên nhân / Cách xử lý |
| :--- | :--- |
| `Access denied for user 'root'` | Sai mật khẩu trong `config/database.php`; kiểm tra lại mật khẩu MariaDB/MySQL. |
| `Connection refused` / port 3306 không mở | Chưa Start MySQL trong XAMPP Control Panel. |
| Port 3306 bị chiếm | MySQL80 và XAMPP MariaDB đang chạy cùng lúc — chỉ bật một cái. |
| MariaDB báo ready nhưng không kết nối được | Kiểm tra `bind-address="127.0.0.1"` trong `E:\xampp\mysql\bin\my.ini`. |
| Import lỗi trên PowerShell | PowerShell không hỗ trợ `<`; dùng CMD hoặc phpMyAdmin. |
| Trang web trắng | `public/index.php` chưa được triển khai (xem mục 1). |
