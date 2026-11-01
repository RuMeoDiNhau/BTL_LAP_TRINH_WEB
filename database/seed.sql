-- ============================================================================
-- DỰ ÁN: DECOR E-COMMERCE
-- FILE : database/seed.sql
-- DBMS : MySQL 8.0 / MariaDB 10.4+
-- MÔ TẢ : Dữ liệu mẫu cho decor_db (tài khoản, danh mục, cấu hình).
-- LƯU Ý : Dùng INSERT IGNORE nên có thể chạy lại nhiều lần mà không bị lỗi
--         trùng khóa và KHÔNG ghi đè dữ liệu đã tồn tại trong CSDL.
--         Phải import database/schema.sql trước khi chạy file này.
-- ============================================================================

USE `decor_db`;

SET NAMES utf8mb4;
SET time_zone = '+07:00';


-- ============================================================================
-- 1. TÀI KHOẢN MẶC ĐỊNH
--    Admin    : admin@decor.vn    / admin123
--    Customer : customer@decor.vn / 123456
--    (Mật khẩu bên dưới được băm bằng password_hash(..., PASSWORD_BCRYPT))
-- ============================================================================
INSERT IGNORE INTO `users`
    (`full_name`, `email`, `password_hash`, `phone`, `avatar`, `role`, `status`)
VALUES
    ('Quản trị viên', 'admin@decor.vn',
     '$2y$10$Yu2iYGmSEciAfClA1/EGnOiSomhonEIy1GH3P/9.qhjA5OC6nUOp.',
     '0901234567', NULL, 'admin', 'active'),
    ('Khách hàng', 'customer@decor.vn',
     '$2y$10$Ff46XLBpen7ZfRvBWjAOd.yZjdFAxBVqxJEhzyXpRptQ3G4LNHOjO',
     '0912345678', NULL, 'member', 'active');


-- ============================================================================
-- 2. DANH MỤC SẢN PHẨM GỐC (parent_id = NULL)
-- ============================================================================
INSERT IGNORE INTO `categories` (`parent_id`, `name`, `slug`, `image`)
VALUES
    (NULL, 'Trang trí phòng khách', 'trang-tri-phong-khach', NULL),
    (NULL, 'Trang trí phòng ngủ',   'trang-tri-phong-ngu',   NULL),
    (NULL, 'Đồ trang trí nội thất', 'do-trang-tri-noi-that', NULL);


-- ============================================================================
-- 3. CẤU HÌNH WEBSITE CƠ BẢN
-- ============================================================================
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`)
VALUES
    ('site_name', 'DECOR E-COMMERCE'),
    ('phone',     '0901234567'),
    ('email',     'contact@decor.vn'),
    ('address',   '123 Nguyễn Văn Cừ, Phường Cửa Nam, Quận Hoàn Kiếm, Hà Nội');


-- ============================================================================
-- KẾT THÚC FILE
-- ============================================================================
