-- ============================================================================
-- PROJECT: DECOR E-COMMERCE
-- FILE   : database/seed_about_faq.sql
-- DESC   : Nội dung mẫu cho trang About và FAQ (phần #2). Chạy SAU schema.sql.
--          INSERT IGNORE: giữ nguyên dữ liệu admin đã sửa, chỉ thêm dòng mới.
--          page_contents có UNIQUE(page_key, section_key); faqs dùng id 1..9.
-- ============================================================================

USE `decor_db`;
SET NAMES utf8mb4;


-- ----------------------------------------------------------------------------
-- 1. NỘI DUNG TRANG ABOUT
-- ----------------------------------------------------------------------------
INSERT IGNORE INTO `page_contents`
    (`page_key`, `section_key`, `title`, `content`, `image`, `sort_order`)
VALUES
    ('about', 'intro_1', NULL,
     'DECOR là điểm đến dành cho những ai yêu thích không gian sống đẹp và thoải mái.',
     NULL, 1),
    ('about', 'intro_2', NULL,
     'Chúng tôi tin rằng mỗi chi tiết nhỏ đều góp phần tạo nên một ngôi nhà thư giãn và ấm cúng hơn.',
     NULL, 2),
    ('about', 'intro_3', NULL,
     'Đội ngũ của chúng tôi luôn lắng nghe trước và đồng hành cùng bạn từ ý tưởng đầu tiên đến từng chi tiết cuối cùng.',
     NULL, 3),

    ('about', 'journey', 'Khởi đầu hành trình',
     'DECOR bắt đầu từ một mong muốn giản dị: mang những ý tưởng trang trí tinh tế đến với mọi ngôi nhà. Chúng tôi không ngừng học hỏi và lắng nghe để mỗi sản phẩm, mỗi gợi ý đều phù hợp với nhịp sống hiện đại.',
     'assets/images/post-thumbnail-1.jpg', 1),
    ('about', 'who_we_are', 'Chúng tôi là ai?',
     'Chúng tôi là những người yêu nội thất, mong muốn mang đến cho mỗi căn phòng sự ấm áp và dễ sống hơn. DECOR làm việc bằng sự tận tâm, cởi mở và tôn trọng lựa chọn của từng khách hàng.',
     'assets/images/post-thumbnail-2.jpg', 2),

    ('about', 'testimonial_1', 'Khách hàng DECOR',
     'Lời tư vấn nhẹ nhàng và dễ áp dụng; tôi thấy mình được lắng nghe từ đầu đến cuối.',
     NULL, 1),
    ('about', 'testimonial_2', 'Đối tác DECOR',
     'Những gợi ý nhỏ nhưng tinh tế giúp phòng khách của tôi gọn gàng hơn hẳn.',
     NULL, 2),
    ('about', 'testimonial_3', 'Người yêu trang trí nhà',
     'Tôi thích cách DECOR chú ý đến chất liệu và cảm giác sử dụng hằng ngày.',
     NULL, 3),
    ('about', 'testimonial_4', 'Thành viên cộng đồng DECOR',
     'Sự hỗ trợ sau khi mua hàng khiến tôi hoàn toàn yên tâm khi chọn DECOR.',
     NULL, 4),

    ('about', 'feature_1', 'Đặt lịch hẹn',
     'Đặt lịch tư vấn để chúng tôi hỗ trợ bạn chi tiết hơn.',
     NULL, 1),
    ('about', 'feature_2', 'Nhận hàng tại cửa hàng',
     'Chọn sản phẩm và nhận trực tiếp tại cửa hàng của chúng tôi.',
     NULL, 2),
    ('about', 'feature_3', 'Đóng gói quà tặng',
     'Món quà nào cũng được gói bằng sự cẩn trọng và tinh tế.',
     NULL, 3),
    ('about', 'feature_4', 'Trả hàng miễn phí toàn cầu',
     'Trả hàng dễ dàng theo chính sách chung của chúng tôi.',
     NULL, 4),

    ('faq', 'intro', 'Các câu hỏi thường gặp',
     'Dưới đây là những câu hỏi chúng tôi nhận được nhiều nhất. Nếu chưa tìm thấy câu trả lời, hãy liên hệ để được hỗ trợ.',
     NULL, 1),

    ('about', 'hero', 'Về chúng tôi', NULL, NULL, 0),
    ('about', 'seo', 'Về DECOR | DECOR',
     'Tìm hiểu về DECOR, người bạn đồng hành trong trang trí và nội thất nhà ở.', NULL, 99),
    ('faq', 'hero', 'Câu hỏi thường gặp', NULL, NULL, 0),
    ('faq', 'ask', 'Đặt câu hỏi cho chúng tôi',
     'Bạn cũng có thể gửi tin nhắn bên dưới — chúng tôi luôn sẵn lòng giúp đỡ.', NULL, 2),
    ('faq', 'seo', 'Câu hỏi thường gặp | DECOR',
     'Giải đáp các câu hỏi thường gặp về lựa chọn sản phẩm, giao hàng, trả hàng và thanh toán tại DECOR.', NULL, 99);


-- ----------------------------------------------------------------------------
-- 2. CÂU HỎI THƯỜNG GẶP (id cố định 1..9)
-- ----------------------------------------------------------------------------
INSERT IGNORE INTO `faqs`
    (`id`, `question`, `answer`, `sort_order`, `is_active`)
VALUES
    (1, 'Tôi có cần tư vấn trước khi chọn sản phẩm không?',
     'Bạn chỉ cần mô tả không gian và sở thích của mình. Chúng tôi sẽ gợi ý những lựa chọn phù hợp nhất.',
     1, 1),
    (2, 'DECOR có hỗ trợ thiết kế phòng không?',
     'Có. Chúng tôi sẵn sàng trao đổi ý tưởng, bảng màu và cách bố trí để không gian của bạn hài hòa hơn.',
     2, 1),
    (3, 'Tôi có thể xem mẫu sản phẩm ở đâu?',
     'Bạn có thể xem các sản phẩm được giới thiệu trên website hoặc liên hệ để biết thêm chi tiết.',
     3, 1),
    (4, 'Thời gian giao hàng được thông báo thế nào?',
     'Chúng tôi sẽ thống nhất thông tin giao hàng rõ ràng trước khi bạn xác nhận lựa chọn.',
     4, 1),
    (5, 'Tôi có thể đổi hoặc trả hàng không?',
     'Chúng tôi mong bạn hài lòng với sản phẩm. Nếu có vấn đề phát sinh, hãy liên hệ để được hướng dẫn theo chính sách chung.',
     5, 1),
    (6, 'Thông tin cá nhân của tôi được xử lý ra sao?',
     'Thông tin bạn cung cấp chỉ dùng để hỗ trợ và liên hệ với bạn. Chúng tôi không chuyển cho bên thứ ba khi chưa có sự đồng ý.',
     6, 1),
    (7, 'Có những phương thức thanh toán nào?',
     'Chúng tôi hỗ trợ các phương thức thanh toán phổ biến và sẽ xác nhận chi tiết khi bạn hoàn tất đơn hàng.',
     7, 1),
    (8, 'Tôi nên làm gì khi sản phẩm hết hàng?',
     'Hãy liên hệ để chúng tôi báo khi có lựa chọn tương tự phù hợp với nhu cầu của bạn.',
     8, 1),
    (9, 'Tôi có thể gửi góp ý hoặc khiếu nại ở đâu?',
     'Chúng tôi trân trọng mọi ý kiến. Bạn chỉ cần để lại tin nhắn và chúng tôi sẽ phản hồi sớm nhất có thể.',
     9, 1);


-- ============================================================================
-- END OF FILE
-- ============================================================================
