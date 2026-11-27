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
    ('address',   '123 Nguyễn Văn Cừ, Phường Cửa Nam, Quận Hoàn Kiếm, Hà Nội'),
    ('comment_moderation', '0');


-- ============================================================================
-- 4. BÀI VIẾT / TIN TỨC MẪU (PHẦN #4)
-- ============================================================================
INSERT IGNORE INTO `posts`
    (`author_id`, `title`, `slug`, `summary`, `content`, `thumbnail`, `meta_title`, `meta_description`, `meta_keywords`, `status`, `views`)
VALUES
    (1, '7 Xu hướng bài trí phòng khách tối giản ấm cúng năm 2026', 'xu-huong-phong-khach-toi-gian-2026',
     'Kết hợp tông màu đá ấm, sofa vải bố tự nhiên và ánh sáng gián tiếp giúp không gian tiếp khách rộng rãi và sang trọng hơn.',
     '<p>Phong cách tối giản năm 2026 chuyển dịch mạnh sang bảng màu <strong>Warm Minimalism</strong> gồm kem sữa, xám đá và nâu gỗ sồi.</p><h2>1. Ưu tiên chất liệu tự nhiên</h2><p>Sofa bọc vải linen dệt thô và bàn trà gỗ nguyên khối giúp phòng khách giữ được cảm giác gần gũi.</p><blockquote>Ít đồ đạc hơn nhưng mỗi món đồ đều có câu chuyện về chất liệu và tỉ lệ chuẩn xác.</blockquote>',
     'assets/images/posts/post-1.svg', '7 Xu hướng phòng khách tối giản 2026', 'Bí quyết bài trí phòng khách tối giản ấm cúng với tông màu đá ấm và gỗ tự nhiên.', 'phòng khách tối giản, decor 2026, nội thất', 'published', 1248),
    (1, 'Cách chọn đèn thả bàn ăn chuẩn tỷ lệ và nhiệt độ màu', 'chon-den-tha-ban-an-chuan-ty-le',
     'Hướng dẫn tính chiều cao treo đèn, đường kính chao đèn theo chiều dài bàn ăn giúp bữa cơm gia đình thêm ấm áp.',
     '<p>Ánh sáng trên bàn ăn nên chọn chỉ số hoàn màu <strong>CRI &ge; 90</strong> và nhiệt độ màu 2700K–3000K.</p><h2>Khoảng cách treo đèn lý tưởng</h2><p>Đáy chao đèn nên cách mặt bàn ăn từ 75cm đến 85cm để ánh sáng tập trung xuống mặt bàn.</p>',
     'assets/images/posts/post-2.svg', 'Chọn đèn thả bàn ăn chuẩn tỷ lệ', 'Hướng dẫn chọn chiều cao treo đèn và nhiệt độ màu cho bàn ăn gia đình.', 'đèn thả bàn ăn, ánh sáng nội thất', 'published', 980),
    (1, 'Phân biệt gỗ sồi tự nhiên và gỗ óc chó khi chọn nội thất', 'phan-biet-go-soi-va-go-oc-cho',
     'So sánh vân gỗ, độ bền, khả năng chống cong vênh và mức chi phí giữa hai dòng gỗ được ưa chuộng nhất hiện nay.',
     '<p>Gỗ sồi nổi bật với sắc vàng sáng trẻ trung; trong khi gỗ óc chó mang sắc nâu trầm sang trọng cùng vân cuộn xoáy độc bản.</p>',
     'assets/images/posts/post-3.svg', 'So sánh gỗ sồi và gỗ óc chó', 'Phân biệt chi tiết gỗ sồi tự nhiên và gỗ óc chó khi mua đồ nội thất.', 'gỗ sồi, gỗ óc chó, nội thất gỗ', 'published', 1520),
    (1, 'Bí quyết thiết kế phòng ngủ phong cách Bắc Âu dễ ngủ sâu', 'thiet-ke-phong-ngu-bac-au-thu-gian',
     'Từ cách chọn rèm 2 lớp cản sáng, ga giường cotton thoáng khí đến việc bố trí tab đầu giường gọn gàng.',
     '<p>Phòng ngủ chuẩn Bắc Âu đặt sự thư thái lên hàng đầu thông qua bảng màu trung tính dịu mắt và ánh sáng tự nhiên dồi dào.</p>',
     'assets/images/posts/post-4.svg', 'Thiết kế phòng ngủ Bắc Âu', 'Gợi ý bố trí phòng ngủ phong cách Scandinavian giúp ngủ sâu và thư giãn.', 'phòng ngủ bắc âu, scandinavian', 'published', 845),
    (1, '5 Mẹo bố trí kệ sách kiêm vách ngăn cho căn hộ dưới 60m2', 'toi-uu-ke-sach-cho-can-ho-nho',
     'Giải pháp phân chia không gian thông minh không cần xây tường bí bách, vừa tăng diện tích lưu trữ vừa tạo điểm nhấn decor.',
     '<p>Kệ sách khung mảnh không hậu lưng giúp ánh sáng vẫn xuyên qua giữa phòng khách và góc làm việc.</p>',
     'assets/images/posts/post-5.svg', 'Bố trí kệ sách cho căn hộ nhỏ', '5 mẹo dùng kệ sách làm vách ngăn thông minh cho chung cư dưới 60m2.', 'kệ sách, căn hộ nhỏ, vách ngăn', 'published', 712),
    (1, 'Top 6 loại cây xanh nội thất dễ chăm sóc, lọc không khí tốt', 'cay-xanh-noi-that-de-cham-soc',
     'Gợi ý các loài cây chịu bóng râm tốt như Bàng Singapore, Trầu bà lá xẻ, Lưỡi hổ cùng cách chọn chậu gốm đồng bộ.',
     '<p>Một chậu cây xanh đặt ở góc tường trống hoặc cạnh sofa mang lại sức sống tức thì và làm mềm kiến trúc trong nhà.</p>',
     'assets/images/posts/post-6.svg', 'Top 6 cây xanh nội thất dễ chăm', 'Các loại cây xanh trong nhà dễ chăm sóc và lọc không khí hiệu quả.', 'cây xanh nội thất, chậu gốm decor', 'published', 1105),
    (1, 'Nên chọn sofa vải bố hay sofa da cho gia đình có trẻ nhỏ?', 'chon-sofa-vai-hay-sofa-da',
     'Đánh giá thực tế độ thoáng mát, khả năng vệ sinh vết bẩn và tuổi thọ trung bình của từng loại bọc sofa.',
     '<p>Sofa da dễ lau nước đổ nhưng cần dưỡng định kỳ; sofa vải nỉ chống thấm thế hệ mới có thể tháo vỏ giặt máy tiện lợi.</p>',
     'assets/images/posts/post-1.svg', 'Chọn sofa vải hay sofa da', 'So sánh sofa vải bố và sofa da cho gia đình có trẻ nhỏ.', 'sofa vải, sofa da, phòng khách', 'published', 640),
    (1, 'Nguyên tắc chọn kích thước và phối màu thảm trải sàn phòng khách', 'phoi-mau-tham-trai-san-phong-khach',
     'Tránh lỗi chọn thảm quá nhỏ khiến khu vực tiếp khách bị rời rạc; cách cân bằng họa tiết thảm với rèm cửa.',
     '<p>Tối thiểu hai chân trước của ghế sofa phải đặt lên trên mép thảm để toàn bộ cụm bàn ghế liên kết thành một khối.</p>',
     'assets/images/posts/post-4.svg', 'Chọn thảm trải sàn phòng khách', 'Nguyên tắc chọn kích thước và phối màu thảm trải sàn chuẩn đẹp.', 'thảm trải sàn, decor phòng khách', 'draft', 0),
    (1, 'Thiết kế góc làm việc tại nhà gọn gàng và tăng tập trung', 'decor-goc-lam-viec-tai-nha',
     'Bố trí bàn gỗ gần cửa sổ, khay giấu dây điện thông minh và đèn bàn chống lóa cho người làm việc từ xa.',
     '<p>Đặt màn hình vuông góc với cửa sổ giúp tận dụng ánh sáng tự nhiên mà không bị chói màn hình vào buổi trưa.</p>',
     'assets/images/posts/post-5.svg', 'Decor góc làm việc tại nhà', 'Cách bố trí góc làm việc tại nhà gọn gàng, đẹp và tối ưu năng suất.', 'góc làm việc, bàn gỗ, decor', 'draft', 0);


-- ============================================================================
-- 5. BÌNH LUẬN MẪU (PHẦN #4)
-- ============================================================================
INSERT IGNORE INTO `comments` (`id`, `post_id`, `user_id`, `content`, `status`)
VALUES
    (1, 1, 2, 'Bài viết rất chi tiết! Mình vừa áp dụng cách phân tầng ánh sáng 3000K cho góc sofa, phòng nhìn ấm hẳn lên.', 'approved'),
    (2, 1, 2, 'Phòng khách rộng khoảng 18m2 thì nên chọn sofa dài bao nhiêu để không bị chật ạ?', 'pending'),
    (3, 2, 2, 'Mình rất thích gợi ý khoảng cách treo đèn 75–85cm, cảm ơn Decor đã chia sẻ.', 'approved'),
    (4, 3, 2, 'Gỗ sồi sơn lau dầu có bị ngả màu sau 3 năm sử dụng không shop?', 'pending'),
    (5, 3, 2, 'Bình luận quảng cáo không liên quan nội dung bài viết.', 'hidden');


-- ============================================================================
-- KẾT THÚC FILE
-- ============================================================================
