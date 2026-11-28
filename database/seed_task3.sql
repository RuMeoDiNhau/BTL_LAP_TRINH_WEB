-- Chọn database dự án và import database/seed.sql trước để có các danh mục.
-- Các slug hiện có giữ dữ liệu, ảnh và tồn kho đã chỉnh.
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT IGNORE INTO products (category_id, name, slug, description, price, sale_price, stock, thumbnail)
SELECT c.id, demo.name, demo.slug, demo.description, demo.price, demo.sale_price, demo.stock,
       CONCAT('decor-template/decor/images/', demo.image_file)
FROM (
    SELECT 'do-trang-tri-noi-that' AS category_slug, 'Ghế cam Terra' AS name, 'ghe-cam-terra' AS slug,
           'Ghế đúc màu cam với tay vịn vuông, hợp với không gian mang tông trung tính.' AS description,
           1590000 AS price, 1290000 AS sale_price, 14 AS stock, 'product-portrait-thumbnail-1.jpg' AS image_file
    UNION ALL SELECT 'do-trang-tri-noi-that', 'Ghế vàng Amber', 'ghe-vang-amber',
           'Mặt ghế cong màu vàng mù tạt và khung thanh mảnh cho góc ăn nhỏ.', 990000, NULL, 0, 'product-portrait-thumbnail-3.jpg'
    UNION ALL SELECT 'trang-tri-phong-khach', 'Ghế đệm xám Slate', 'ghe-dem-xam-slate',
           'Ghế đệm vải xám với lưng tựa cao, phù hợp cho góc đọc sách.', 2490000, 2190000, 8, 'product-portrait-thumbnail-4.jpg'
    UNION ALL SELECT 'do-trang-tri-noi-that', 'Ghế bar Onyx', 'ghe-bar-onyx',
           'Ghế đen chân gỗ có thanh gác chân, dùng cho đảo bếp hoặc bàn cao.', 1190000, NULL, 16, 'product2.jpg'
    UNION ALL SELECT 'trang-tri-phong-ngu', 'Ghế bập bênh Mộc', 'ghe-bap-benh-moc',
           'Ghế bập bênh gỗ tông mật ong cho góc thư giãn cạnh cửa sổ.', 2790000, 2490000, 9, 'product1.jpg'
    UNION ALL SELECT 'trang-tri-phong-ngu', 'Giường bọc vải Dune', 'giuong-boc-vai-dune',
           'Khung giường thấp, đầu giường bọc vải xám nâu và đường nét gọn gàng.', 8990000, NULL, 6, 'product-thumb-11.jpg'
    UNION ALL SELECT 'do-trang-tri-noi-that', 'Ghế chân gỗ Cloud', 'ghe-chan-go-cloud',
           'Mặt ghế trắng ôm nhẹ phần lưng, phối chân gỗ tự nhiên.', 1290000, 1090000, 20, 'product-portrait-thumbnail-2.jpg'
    UNION ALL SELECT 'trang-tri-phong-khach', 'Kệ thấp Sồi', 'ke-thap-soi',
           'Kệ gỗ màu sáng có ngăn mở, dùng để đặt sách và đồ trang trí.', 3290000, NULL, 12, 'product-thumb-12.jpg'
    UNION ALL SELECT 'trang-tri-phong-khach', 'Sofa vải Linen', 'sofa-vai-linen',
           'Sofa đệm sáng màu, chân gỗ thấp phù hợp với phòng khách nhỏ.', 7990000, 6990000, 10, 'product-thumbnail-1.jpg'
) AS demo
JOIN categories c ON c.slug = demo.category_slug;
