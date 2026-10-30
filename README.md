# DECOR E-COMMERCE

Website Thương mại Điện tử / Doanh nghiệp Đồ trang trí & Nội thất.  
Dự án được xây dựng theo mô hình **MVC bằng PHP thuần (Native PHP >= 7.0)** kết hợp với **MySQL**, không sử dụng PHP Framework hay CMS.

---

## 1. Yêu cầu môi trường

- **PHP:** Phiên bản `>= 7.0` (Khuyên dùng PHP 8.x).
- **Cơ sở dữ liệu:** MySQL hoặc MariaDB.
- **Web Server:** Web Server tích hợp của PHP (Built-in Server) hoặc thông qua phần mềm giả lập như **XAMPP, Laragon, WampServer, MAMP**.

---

## 2. Hướng dẫn cài đặt & Khởi chạy

### Bước 1: Thiết lập Cơ sở dữ liệu (MySQL)
1. Mở công cụ quản trị MySQL của bạn (phpMyAdmin, DBeaver, HeidiSQL hoặc MySQL CLI).
2. Tạo mới một cơ sở dữ liệu có tên `decor_db` với bảng mã `utf8mb4_unicode_ci`:
   ```sql
   CREATE DATABASE IF NOT EXISTS decor_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Import file cấu trúc CSDL và dữ liệu khởi tạo tại:
   ```text
   database/schema.sql
   ```

---

### Bước 2: Cấu hình kết nối CSDL
Mở file `config/database.php` và điều chỉnh lại thông số kết nối cho phù hợp với môi trường máy của bạn (nếu MySQL có mật khẩu):
```php
return [
    'host'     => '127.0.0.1',
    'port'     => '3306',
    'dbname'   => 'decor_db',
    'username' => 'root',     // Thay đổi nếu MySQL dùng user khác
    'password' => '',         // Nhập mật khẩu MySQL của máy bạn (nếu có)
    'charset'  => 'utf8mb4',
    // ...
];
```

---

### Bước 3: Khởi chạy ứng dụng

Chạy dự án bằng cách:

#### Sử dụng PHP Built-in Server (Nhanh nhất)
Mở Terminal tại thư mục này và chạy lệnh:
```bash
php -S localhost:8000 -t public
```
Sau đó truy cập trình duyệt:
- **Trang người dùng (Client):** [http://localhost:8000](http://localhost:8000)
- **Trang quản trị (Admin):** [http://localhost:8000/admin](http://localhost:8000/admin)


---

## 3. Tài khoản đăng nhập kiểm thử

Dữ liệu mẫu từ `database/schema.sql` cung cấp sẵn các tài khoản sau:

| Vai trò | Email đăng nhập | Mật khẩu mặc định |
| :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@decor.vn` | `admin123` |
| **Khách hàng (Customer)** | `customer@decor.vn` | `123456` |

---

## 4. Cấu trúc thư mục mã nguồn

```text
├── app/
│   ├── Controllers/
│   │   ├── Client/             # Controllers xử lý các trang phía người dùng
│   │   └── Admin/              # Controllers xử lý các trang phía quản trị
│   ├── Models/                 # Các Model tương tác cơ sở dữ liệu qua PDO
│   └── Views/
│       ├── layouts/            # Layout dùng chung (client.php, admin.php)
│       ├── client/             # Giao diện phía người dùng
│       └── admin/              # Giao diện phía quản trị
├── config/
│   └── database.php            # Cấu hình kết nối MySQL PDO
├── core/
│   ├── Controller.php          # Base Controller (render view, json, redirect)
│   ├── Database.php            # PDO Singleton connection
│   ├── Router.php              # Bộ điều hướng URL
│   └── Helpers.php             # Các hàm tiện ích dùng chung
├── database/
│   └── schema.sql              # CSDL 13 bảng quan hệ và dữ liệu mẫu
└── public/
    ├── index.php               # Điểm đón request duy nhất của ứng dụng
    ├── .htaccess               # Cấu hình URL Rewrite cho Apache
    ├── assets/                 # File tĩnh (CSS, JS, Fonts, Images)
    └── uploads/                # Thư mục lưu hình ảnh tải lên máy chủ
```
