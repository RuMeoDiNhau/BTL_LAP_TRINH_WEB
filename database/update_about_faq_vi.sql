-- ============================================================================
-- PROJECT: DECOR E-COMMERCE
-- FILE   : database/update_about_faq_vi.sql
-- DESC   : Cập nhật nội dung #2 sang tiếng Việt cho CSDL ĐÃ IMPORT seed cũ.
--          - Chỉ UPDATE, không INSERT, không xoá, không đổi image/sort_order.
--          - page_contents: khóa theo (page_key, section_key) với page_key about/faq.
--          - faqs: khóa theo sort_order 1..9 (đúng thứ tự bản seed).
-- CÁCH DÙNG (sau khi đã backup):
--   mysql -u <user> -p decor_db < database/update_about_faq_vi.sql
--   hoặc mở MySQL Workbench và chạy toàn bộ file này.
-- LƯU Ý: lệnh này GHI ĐÈ nội dung tiếng Anh hiện có. Không chạy nếu admin
--         đã tự chỉnh nội dung tiếng Việt khác đi.
-- ============================================================================

USE `decor_db`;
SET NAMES utf8mb4;

-- ----------------------------------------------------------------------------
-- 1. PAGE CONTENTS – ABOUT
-- ----------------------------------------------------------------------------
UPDATE `page_contents` SET `title` = 'Về chúng tôi', `content` = NULL
WHERE `page_key` = 'about' AND `section_key` = 'hero';

UPDATE `page_contents` SET `content` = 'DECOR là điểm đến dành cho những ai yêu thích không gian sống đẹp và thoải mái.'
WHERE `page_key` = 'about' AND `section_key` = 'intro_1';

UPDATE `page_contents` SET `content` = 'Chúng tôi tin rằng mỗi chi tiết nhỏ đều góp phần tạo nên một ngôi nhà thư giãn và ấm cúng hơn.'
WHERE `page_key` = 'about' AND `section_key` = 'intro_2';

UPDATE `page_contents` SET `content` = 'Đội ngũ của chúng tôi luôn lắng nghe trước và đồng hành cùng bạn từ ý tưởng đầu tiên đến từng chi tiết cuối cùng.'
WHERE `page_key` = 'about' AND `section_key` = 'intro_3';

UPDATE `page_contents` SET `title` = 'Khởi đầu hành trình',
    `content` = 'DECOR bắt đầu từ một mong muốn giản dị: mang những ý tưởng trang trí tinh tế đến với mọi ngôi nhà. Chúng tôi không ngừng học hỏi và lắng nghe để mỗi sản phẩm, mỗi gợi ý đều phù hợp với nhịp sống hiện đại.'
WHERE `page_key` = 'about' AND `section_key` = 'journey';

UPDATE `page_contents` SET `title` = 'Chúng tôi là ai?',
    `content` = 'Chúng tôi là những người yêu nội thất, mong muốn mang đến cho mỗi căn phòng sự ấm áp và dễ sống hơn. DECOR làm việc bằng sự tận tâm, cởi mở và tôn trọng lựa chọn của từng khách hàng.'
WHERE `page_key` = 'about' AND `section_key` = 'who_we_are';

UPDATE `page_contents` SET `title` = 'Khách hàng DECOR',
    `content` = 'Lời tư vấn nhẹ nhàng và dễ áp dụng; tôi thấy mình được lắng nghe từ đầu đến cuối.'
WHERE `page_key` = 'about' AND `section_key` = 'testimonial_1';

UPDATE `page_contents` SET `title` = 'Đối tác DECOR',
    `content` = 'Những gợi ý nhỏ nhưng tinh tế giúp phòng khách của tôi gọn gàng hơn hẳn.'
WHERE `page_key` = 'about' AND `section_key` = 'testimonial_2';

UPDATE `page_contents` SET `title` = 'Người yêu trang trí nhà',
    `content` = 'Tôi thích cách DECOR chú ý đến chất liệu và cảm giác sử dụng hằng ngày.'
WHERE `page_key` = 'about' AND `section_key` = 'testimonial_3';

UPDATE `page_contents` SET `title` = 'Thành viên cộng đồng DECOR',
    `content` = 'Sự hỗ trợ sau khi mua hàng khiến tôi hoàn toàn yên tâm khi chọn DECOR.'
WHERE `page_key` = 'about' AND `section_key` = 'testimonial_4';

UPDATE `page_contents` SET `title` = 'Đặt lịch hẹn',
    `content` = 'Đặt lịch tư vấn để chúng tôi hỗ trợ bạn chi tiết hơn.'
WHERE `page_key` = 'about' AND `section_key` = 'feature_1';

UPDATE `page_contents` SET `title` = 'Nhận hàng tại cửa hàng',
    `content` = 'Chọn sản phẩm và nhận trực tiếp tại cửa hàng của chúng tôi.'
WHERE `page_key` = 'about' AND `section_key` = 'feature_2';

UPDATE `page_contents` SET `title` = 'Đóng gói quà tặng',
    `content` = 'Món quà nào cũng được gói bằng sự cẩn trọng và tinh tế.'
WHERE `page_key` = 'about' AND `section_key` = 'feature_3';

UPDATE `page_contents` SET `title` = 'Trả hàng miễn phí toàn cầu',
    `content` = 'Trả hàng dễ dàng theo chính sách chung của chúng tôi.'
WHERE `page_key` = 'about' AND `section_key` = 'feature_4';

UPDATE `page_contents` SET `title` = 'Về DECOR | DECOR',
    `content` = 'Tìm hiểu về DECOR, người bạn đồng hành trong trang trí và nội thất nhà ở.'
WHERE `page_key` = 'about' AND `section_key` = 'seo';

-- ----------------------------------------------------------------------------
-- 2. PAGE CONTENTS – FAQ
-- ----------------------------------------------------------------------------
UPDATE `page_contents` SET `title` = 'Câu hỏi thường gặp', `content` = NULL
WHERE `page_key` = 'faq' AND `section_key` = 'hero';

UPDATE `page_contents` SET `title` = 'Các câu hỏi thường gặp',
    `content` = 'Dưới đây là những câu hỏi chúng tôi nhận được nhiều nhất. Nếu chưa tìm thấy câu trả lời, hãy liên hệ để được hỗ trợ.'
WHERE `page_key` = 'faq' AND `section_key` = 'intro';

UPDATE `page_contents` SET `title` = 'Đặt câu hỏi cho chúng tôi',
    `content` = 'Bạn cũng có thể gửi tin nhắn bên dưới — chúng tôi luôn sẵn lòng giúp đỡ.'
WHERE `page_key` = 'faq' AND `section_key` = 'ask';

UPDATE `page_contents` SET `title` = 'Câu hỏi thường gặp | DECOR',
    `content` = 'Giải đáp các câu hỏi thường gặp về lựa chọn sản phẩm, giao hàng, trả hàng và thanh toán tại DECOR.'
WHERE `page_key` = 'faq' AND `section_key` = 'seo';

-- ----------------------------------------------------------------------------
-- 3. FAQS – theo sort_order
-- ----------------------------------------------------------------------------
UPDATE `faqs` SET
    `question` = 'Tôi có cần tư vấn trước khi chọn sản phẩm không?',
    `answer`   = 'Bạn chỉ cần mô tả không gian và sở thích của mình. Chúng tôi sẽ gợi ý những lựa chọn phù hợp nhất.'
WHERE `sort_order` = 1;

UPDATE `faqs` SET
    `question` = 'DECOR có hỗ trợ thiết kế phòng không?',
    `answer`   = 'Có. Chúng tôi sẵn sàng trao đổi ý tưởng, bảng màu và cách bố trí để không gian của bạn hài hòa hơn.'
WHERE `sort_order` = 2;

UPDATE `faqs` SET
    `question` = 'Tôi có thể xem mẫu sản phẩm ở đâu?',
    `answer`   = 'Bạn có thể xem các sản phẩm được giới thiệu trên website hoặc liên hệ để biết thêm chi tiết.'
WHERE `sort_order` = 3;

UPDATE `faqs` SET
    `question` = 'Thời gian giao hàng được thông báo thế nào?',
    `answer`   = 'Chúng tôi sẽ thống nhất thông tin giao hàng rõ ràng trước khi bạn xác nhận lựa chọn.'
WHERE `sort_order` = 4;

UPDATE `faqs` SET
    `question` = 'Tôi có thể đổi hoặc trả hàng không?',
    `answer`   = 'Chúng tôi mong bạn hài lòng với sản phẩm. Nếu có vấn đề phát sinh, hãy liên hệ để được hướng dẫn theo chính sách chung.'
WHERE `sort_order` = 5;

UPDATE `faqs` SET
    `question` = 'Thông tin cá nhân của tôi được xử lý ra sao?',
    `answer`   = 'Thông tin bạn cung cấp chỉ dùng để hỗ trợ và liên hệ với bạn. Chúng tôi không chuyển cho bên thứ ba khi chưa có sự đồng ý.'
WHERE `sort_order` = 6;

UPDATE `faqs` SET
    `question` = 'Có những phương thức thanh toán nào?',
    `answer`   = 'Chúng tôi hỗ trợ các phương thức thanh toán phổ biến và sẽ xác nhận chi tiết khi bạn hoàn tất đơn hàng.'
WHERE `sort_order` = 7;

UPDATE `faqs` SET
    `question` = 'Tôi nên làm gì khi sản phẩm hết hàng?',
    `answer`   = 'Hãy liên hệ để chúng tôi báo khi có lựa chọn tương tự phù hợp với nhu cầu của bạn.'
WHERE `sort_order` = 8;

UPDATE `faqs` SET
    `question` = 'Tôi có thể gửi góp ý hoặc khiếu nại ở đâu?',
    `answer`   = 'Chúng tôi trân trọng mọi ý kiến. Bạn chỉ cần để lại tin nhắn và chúng tôi sẽ phản hồi sớm nhất có thể.'
WHERE `sort_order` = 9;

-- ============================================================================
-- END OF FILE
-- ============================================================================
