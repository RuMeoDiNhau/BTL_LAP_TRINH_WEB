# DECOR E-COMMERCE

Website Thương mại Điện tử / Doanh nghiệp Đồ trang trí & Nội thất.

- **Kiến trúc:** MVC tự viết bằng **PHP thuần**, không dùng PHP Framework, CMF hay CMS.
- **Cơ sở dữ liệu:** MySQL 8.0 hoặc MariaDB 10.4+.
- **Frontend:** HTML / CSS / JavaScript thuần.
- **Zero dependency:** không Composer, không npm, không ORM, không template engine.

---

## 1. Trạng thái dự án

### Đã hoàn thành

- `database/schema.sql` — tạo CSDL `decor_db` và **14 bảng** quan hệ đầy đủ khóa ngoại.
- `database/seed.sql` — dữ liệu mẫu: 2 tài khoản, 3 danh mục, 4 cấu hình.
- `config/database.example.php` — mẫu cấu hình PDO; mỗi máy tạo `config/database.php` riêng, không commit.
- Bộ nền MVC chung gồm `core/`, `public/index.php`, các route theo module trong `routes/` và rewrite Apache.
- Tài khoản cá nhân: đăng ký/đăng nhập bằng phiên PHP; sửa họ tên, email, điện thoại; đổi mật khẩu; upload/thay/xóa avatar lưu trên server.
- Giao diện tài khoản responsive tại `app/Views/client/account/`, CSS/JS tại `public/assets/`.
- Phần riêng #3 có danh mục và chi tiết sản phẩm, giỏ hàng, đặt hàng COD và các màn hình quản trị sản phẩm, giỏ hàng, đơn hàng.
- Đã kiểm thử thực tế trên **XAMPP MariaDB 10.4.32 + PHP 8.2.12**:
  - Kết nối PDO thành công tới `decor_db`.
  - Đủ 14 bảng, 2 user.
  - Hash bcrypt của `admin123` và `123456` kiểm tra bằng `password_verify()` đều đúng.

Trang chủ PHP tại `/` sử dụng giao diện Decor. Xác thực chạy qua `/login` và `/register`, với tài khoản trong MySQL và phiên PHP.

---

## 2. Yêu cầu môi trường

| Thành phần | Yêu cầu |
| :--- | :--- |
| PHP | `>= 8.1` (khuyến nghị 8.2.x) |
| Extension | `pdo_mysql`, `mbstring`, `fileinfo` (thường có sẵn trong XAMPP) |
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

Import hai file nền theo thứ tự dưới đây. Schema tự tạo database.

1. `database/schema.sql` — tạo CSDL `decor_db` (charset `utf8mb4`, collation `utf8mb4_unicode_ci`) và 14 bảng.
2. `database/seed.sql` — thêm dữ liệu mẫu.

Để thêm chín sản phẩm mẫu cho Phần riêng #3, chọn database `decor_db` rồi import `database/seed_task3.sql`.

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

Tạo cấu hình riêng trên mỗi máy nếu chưa có:

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

```bash
php -S localhost:8000 -t public
```

Truy cập:

- **Đăng nhập:** [http://localhost:8000/login](http://localhost:8000/login)
- **Đăng ký:** [http://localhost:8000/register](http://localhost:8000/register)
- **Hồ sơ:** [http://localhost:8000/account/profile](http://localhost:8000/account/profile)
- **Đổi mật khẩu:** [http://localhost:8000/account/change-password](http://localhost:8000/account/change-password)
- **Quản trị:** [http://localhost:8000/admin](http://localhost:8000/admin)

Nếu PHP chưa nằm trong PATH, thay `php` bằng đường dẫn đến `php.exe` trong XAMPP/Laragon. Với Apache, bật `mod_rewrite` và cho phép `.htaccess` trong cấu hình dự án. Live Server chỉ phục vụ bản HTML tĩnh.

Các liên kết tài khoản trong bản HTML demo trỏ vào các route dưới `/public/` khi phục vụ cả project ở localhost. Khi triển khai, đặt DocumentRoot vào `public/`.

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

1. Hoàn thiện các trang Client: Home, Product, Cart, Post, Page (About/Contact/FAQ).
2. Admin: Dashboard, Product, Order, Post, User, Setting; áp dụng kiểm tra quyền PHP trước khi thực thi route quản trị.
3. Tích hợp thống nhất giao diện của các thành viên vào layout chung.
4. Kiểm thử giỏ hàng, đặt hàng, quản trị. Khôi phục mật khẩu qua email chưa được triển khai trong luồng PHP.

---

## 8. Xử lý sự cố

| Lỗi | Nguyên nhân / Cách xử lý |
| :--- | :--- |
| `Access denied for user 'root'` | Sai mật khẩu trong `config/database.php`; kiểm tra lại mật khẩu MariaDB/MySQL. |
| `Connection refused` / port 3306 không mở | Chưa Start MySQL trong XAMPP Control Panel. |
| Port 3306 bị chiếm | MySQL80 và XAMPP MariaDB đang chạy cùng lúc — chỉ bật một cái. |
| MariaDB báo ready nhưng không kết nối được | Kiểm tra `bind-address="127.0.0.1"` trong `E:\xampp\mysql\bin\my.ini`. |
| Import lỗi trên PowerShell | PowerShell không hỗ trợ `<`; dùng CMD hoặc phpMyAdmin. |
| Trang thông báo chưa có CSDL | Tạo `config/database.php`, kiểm tra thông số kết nối, import `schema.sql` rồi `seed.sql`. |
| Không giữ được đăng nhập | Kiểm tra `session.save_path` trong PHP có tồn tại và có quyền ghi. |
| Upload không thành công | Kiểm tra `upload_tmp_dir`, quyền ghi `public/uploads/`, `upload_max_filesize >= 2M` và `post_max_size >= 3M`. |

---

## 9. Tài khoản cá nhân

### Các file chính

```text
app/Controllers/Client/AccountController.php  # Điều phối sửa hồ sơ, mật khẩu, avatar
app/Controllers/Client/AuthController.php     # Đăng ký/đăng nhập/đăng xuất dùng chung
app/Models/User.php                          # Truy vấn users bằng prepared statements
app/Services/AccountValidation.php           # Kiểm tra dữ liệu phía PHP
app/Services/AvatarUpload.php                # Kiểm tra ảnh và quản lý file avatar
app/Views/client/account/                    # Hồ sơ, đổi mật khẩu và sidebar dùng chung
app/Views/layouts/client.php                 # Header/footer và trạng thái người dùng
public/assets/css/pages/account.css         # Giao diện desktop/mobile
public/assets/js/pages/account.js           # Validation, xem trước ảnh, hiện/ẩn mật khẩu
public/uploads/avatars/                      # Avatar được tạo khi upload, không commit
routes/account.php                           # Route hồ sơ, mật khẩu và avatar
ui/account/                                 # Liên kết mở giao diện PHP từ bộ UI
tests/account-smoke.php                      # Kiểm thử HTTP với MySQL thử riêng
```

### Hành vi và tích hợp

- Sửa họ tên, email, số điện thoại (điện thoại không bắt buộc). Đổi email yêu cầu xác nhận mật khẩu hiện tại và kiểm tra email trùng.
- Đổi mật khẩu yêu cầu đúng mật khẩu cũ, mật khẩu mới khác mật khẩu cũ, xác nhận khớp. Mật khẩu mới có chữ hoa, chữ thường, số; từ 8 ký tự và không vượt quá 72 byte để phù hợp bcrypt.
- Upload JPG/PNG/WebP tối đa 2 MB, kích thước tối đa 4096 × 4096. Kiểm tra MIME thực, thông tin ảnh và file HTTP upload; tên file do server tạo ngẫu nhiên. Có xem trước ảnh, hủy lựa chọn, thay ảnh và xóa ảnh cũ.
- Không cần sửa schema: dùng các cột sẵn có trong bảng `users`. Không đưa role, status hoặc ID từ form vào câu lệnh cập nhật hồ sơ.
- `$_SESSION['user']['id']` xác định người đang đăng nhập theo quy ước chung. `sign_in()` tạo phiên và lưu dấu phiên bản mật khẩu. Front controller cập nhật thông tin, trạng thái và quyền từ DB trước khi xử lý route. Phiên cũ được tạo trước khi có dấu phiên bản sẽ cần đăng nhập lại một lần.
- Đổi mật khẩu làm các phiên khác hết hiệu lực ở request tiếp theo. Phiên hiện tại được đổi session ID và CSRF token.
- Tất cả thao tác ghi dùng POST + CSRF; dữ liệu được kiểm tra lại trong PHP và escape khi hiển thị. Mật khẩu không được điền lại vào HTML khi có lỗi.
- Luồng PHP này không sử dụng dữ liệu localStorage của demo `ui/assets/js/pages/auth.js`. Để thử chức năng thật, đăng ký/đăng nhập qua các URL PHP ở mục 3. Không có cơ chế tự chuyển mật khẩu demo sang MySQL.
- `ui/account/*.html` mở cùng giao diện PHP, không duy trì một bản hồ sơ độc lập. Hãy mở qua server PHP; không mở bằng `file://`.

### Route đang có

| Phương thức | Route | Tác vụ |
| :--- | :--- | :--- |
| GET / POST | `/register` | Hiển thị / xử lý đăng ký |
| GET / POST | `/login` | Hiển thị / xử lý đăng nhập |
| POST | `/logout` | Đăng xuất |
| GET / POST | `/account/profile` | Hiển thị / lưu thông tin cá nhân |
| GET / POST | `/account/change-password` | Hiển thị / đổi mật khẩu |
| POST | `/account/avatar` | Upload hoặc xóa avatar |

### Kiểm thử tự động

Chỉ dùng **database thử riêng** có tên bắt đầu bằng `decor_account_test`. Script tự tạo bảng/dữ liệu mẫu từ SQL và dọn dữ liệu tài khoản nó tạo; không được trỏ vào database thật. Máy chủ PHP và script phải dùng cùng cấu hình thử.

Ví dụ PowerShell, cửa sổ thứ nhất:

```powershell
$env:DECOR_DB_HOST = '127.0.0.1'
$env:DECOR_DB_PORT = '3306'
$env:DECOR_DB_DBNAME = 'decor_account_test'
$env:DECOR_DB_USERNAME = 'root'
$env:DECOR_DB_PASSWORD = ''  # Điền thông số của MySQL thử trên máy bạn
php -d post_max_size=3M -d upload_max_filesize=2M -S localhost:8017 -t public
```

Cửa sổ thứ hai đặt cùng các biến `DECOR_DB_*`, rồi chạy (PHP CLI cần extension `curl`):

```powershell
$env:DECOR_TEST_URL = 'http://localhost:8017'
php tests/account-smoke.php
```

Kiểm tra bao gồm: đăng ký/đăng nhập; lưu và từ chối dữ liệu lỗi; email trùng; yêu cầu mật khẩu khi đổi email; CSRF; XSS; giả mạo ID/quyền; upload/thay/xóa ảnh; file sai định dạng/dung lượng/kích thước; đổi mật khẩu; vô hiệu phiên cũ; khóa tài khoản; đăng xuất.

## Phần riêng #3 · Sản phẩm, giỏ hàng và đơn hàng

Giỏ hàng lưu các mặt hàng trong bảng `cart_items` của `database/schema.sql`. Admin có thể đánh dấu giỏ **Đã xem**. Dấu xem được lưu theo tài khoản trong `settings` với khóa `cart_review:<user_id>` và áp dụng cho các mặt hàng, số lượng và giá tại thời điểm xem. Khách có thể mua hàng bình thường ở cả hai trạng thái. [Dữ liệu sản phẩm mẫu](database/seed_task3.sql) dùng ba danh mục từ `database/seed.sql`.

| Đường dẫn | Cách thử |
| :--- | :--- |
| `/products` | Tìm sản phẩm theo tên hoặc mô tả, lọc danh mục và mở trang chi tiết. |
| `/cart`, `/checkout` | Đăng nhập tài khoản khách, thêm sản phẩm, cập nhật số lượng và đặt hàng COD. |
| `/orders` | Xem danh sách và chi tiết đơn hàng của tài khoản đang đăng nhập. |
| `/admin/products` | Tìm, thêm, sửa, tải ảnh và xóa sản phẩm. Sản phẩm đã có trong đơn hàng sẽ được ẩn khi xóa. |
| `/admin/carts` | Tìm khách hàng, xem nội dung giỏ và đánh dấu **Đã xem**. |
| `/admin/orders` | Tìm đơn, xem thông tin giao hàng và cập nhật trạng thái. |

Đơn hàng đi từ **Chờ xác nhận** → **Đã xác nhận** → **Đang giao** → **Hoàn tất**. Quản trị viên có thể hủy đơn ở hai trạng thái đầu. Đặt hàng trừ tồn kho và hủy đơn hoàn tồn trong giao dịch MySQL. Đơn lưu tên và đơn giá tại thời điểm đặt.

```text
routes/shop.php                         # Route khách và quản trị
app/Controllers/Client/                 # Product, Cart, Checkout và Order
app/Controllers/Admin/                  # ProductAdmin, CartAdmin và OrderAdmin
app/Models/                             # Category, Product, Cart và Order
app/Views/client/{products,cart,orders}/ # Các màn hình mua sắm
app/Views/admin/{products,carts,orders}/ # Các màn hình quản trị
public/assets/{css,js}/pages/shop.*      # Giao diện và tương tác
public/uploads/products/                # Ảnh do admin tải lên
database/seed_task3.sql                  # Sản phẩm mẫu
```
