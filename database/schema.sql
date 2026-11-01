-- ============================================================================
-- DỰ ÁN: DECOR E-COMMERCE
-- FILE : database/schema.sql
-- DBMS : MySQL 8.0 / MariaDB 10.4+
-- MÔ TẢ : Tạo CSDL decor_db và toàn bộ bảng, khóa ngoại (chỉ cấu trúc).
-- LƯU Ý : Không xóa dữ liệu cũ. Dữ liệu mẫu nằm ở database/seed.sql.
--         Muốn tạo lại từ đầu: DROP DATABASE decor_db; rồi import lại file này.
-- ============================================================================


-- ============================================================================
-- 1. TẠO CƠ SỞ DỮ LIỆU
-- ============================================================================
CREATE DATABASE IF NOT EXISTS `decor_db`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `decor_db`;

SET NAMES utf8mb4;
SET time_zone = '+07:00';


-- ============================================================================
-- 2. TẠO BẢNG
-- Thứ tự: bảng độc lập -> bảng phụ thuộc.
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 2.1. users - Tài khoản người dùng (admin / khách hàng)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `full_name`     VARCHAR(100)    NOT NULL,
    `email`         VARCHAR(150)    NOT NULL,
    `password_hash` VARCHAR(255)    NOT NULL,
    `phone`         VARCHAR(20)     DEFAULT NULL,
    `avatar`        VARCHAR(255)    DEFAULT NULL,
    `role`          ENUM('admin', 'member')             NOT NULL DEFAULT 'member',
    `status`        ENUM('active', 'banned')            NOT NULL DEFAULT 'active',
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                    ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
-- 2.2. settings - Cấu hình chung của website (key / value)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `setting_key`   VARCHAR(100)    NOT NULL,
    `setting_value` TEXT            DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_settings_setting_key` (`setting_key`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
-- 2.3. page_contents - Nội dung các khối của trang chủ / giới thiệu
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `page_contents` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `page_key`      VARCHAR(50)     NOT NULL,
    `section_key`   VARCHAR(50)     NOT NULL,
    `title`         VARCHAR(255)    DEFAULT NULL,
    `content`       TEXT            DEFAULT NULL,
    `image`         VARCHAR(255)    DEFAULT NULL,
    `sort_order`    INT             NOT NULL DEFAULT 0,
    `updated_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                    ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_page_contents_page_section` (`page_key`, `section_key`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
-- 2.4. contacts - Khách để lại thông tin liên hệ
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contacts` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`       INT UNSIGNED    DEFAULT NULL,
    `name`          VARCHAR(100)    NOT NULL,
    `email`         VARCHAR(150)    NOT NULL,
    `phone`         VARCHAR(20)     DEFAULT NULL,
    `subject`       VARCHAR(200)    DEFAULT NULL,
    `message`       TEXT            NOT NULL,
    `status`        ENUM('unread', 'read', 'replied')   NOT NULL DEFAULT 'unread',
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_contacts_status` (`status`),
    KEY `idx_contacts_created_at` (`created_at`),
    KEY `idx_contacts_user_id` (`user_id`),
    CONSTRAINT `fk_contacts_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
-- 2.5. faqs - Câu hỏi thường gặp
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `faqs` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `question`      VARCHAR(255)    NOT NULL,
    `answer`        TEXT            NOT NULL,
    `sort_order`    INT             NOT NULL DEFAULT 0,
    `is_active`     TINYINT(1)      NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
-- 2.6. categories - Danh mục sản phẩm (hỗ trợ danh mục cha - con)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `parent_id`     INT UNSIGNED    DEFAULT NULL,
    `name`          VARCHAR(100)    NOT NULL,
    `slug`          VARCHAR(120)    NOT NULL,
    `image`         VARCHAR(255)    DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_categories_slug` (`slug`),
    KEY `idx_categories_parent_id` (`parent_id`),
    CONSTRAINT `fk_categories_parent`
        FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
-- 2.7. products - Sản phẩm
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `category_id`   INT UNSIGNED    NOT NULL,
    `name`          VARCHAR(200)    NOT NULL,
    `slug`          VARCHAR(220)    NOT NULL,
    `description`   TEXT            DEFAULT NULL,
    `price`         INT UNSIGNED    NOT NULL,
    `sale_price`    INT UNSIGNED    DEFAULT NULL,
    `stock`         INT UNSIGNED    NOT NULL DEFAULT 0,
    `thumbnail`     VARCHAR(255)    DEFAULT NULL,
    `status`        ENUM('active', 'hidden')            NOT NULL DEFAULT 'active',
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                    ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_products_slug` (`slug`),
    KEY `idx_products_category_id` (`category_id`),
    KEY `idx_products_status` (`status`),
    KEY `idx_products_created_at` (`created_at`),
    KEY `idx_products_name` (`name`),
    CONSTRAINT `fk_products_category`
        FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
-- 2.8. product_images - Ảnh phụ của sản phẩm
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_images` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `product_id`    INT UNSIGNED    NOT NULL,
    `image_path`    VARCHAR(255)    NOT NULL,
    `sort_order`    INT             NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_product_images_product_id` (`product_id`),
    CONSTRAINT `fk_product_images_product`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
-- 2.9. reviews - Đánh giá sản phẩm (1 người / 1 sản phẩm / 1 lần)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reviews` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `product_id`    INT UNSIGNED    NOT NULL,
    `user_id`       INT UNSIGNED    NOT NULL,
    `rating`        TINYINT UNSIGNED NOT NULL,
    `content`       TEXT            DEFAULT NULL,
    `status`        ENUM('pending', 'approved', 'hidden') NOT NULL DEFAULT 'approved',
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_reviews_product_user` (`product_id`, `user_id`),
    KEY `idx_reviews_user_id` (`user_id`),
    KEY `idx_reviews_status` (`status`),
    CONSTRAINT `chk_reviews_rating` CHECK (`rating` BETWEEN 1 AND 5),
    CONSTRAINT `fk_reviews_product`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_reviews_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
-- 2.10. cart_items - Giỏ hàng (lưu theo tài khoản)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cart_items` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`       INT UNSIGNED    NOT NULL,
    `product_id`    INT UNSIGNED    NOT NULL,
    `quantity`      INT UNSIGNED    NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_cart_items_user_product` (`user_id`, `product_id`),
    KEY `idx_cart_items_product_id` (`product_id`),
    CONSTRAINT `fk_cart_items_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_cart_items_product`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
-- 2.11. orders - Đơn hàng
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`           INT UNSIGNED    NOT NULL,
    `order_code`        VARCHAR(30)     NOT NULL,
    `receiver_name`     VARCHAR(100)    NOT NULL,
    `phone`             VARCHAR(20)     NOT NULL,
    `address`           VARCHAR(255)    NOT NULL,
    `note`              VARCHAR(255)    DEFAULT NULL,
    `total`             INT UNSIGNED    NOT NULL,
    `payment_method`    ENUM('cod', 'bank_transfer')        NOT NULL DEFAULT 'cod',
    `status`            ENUM('pending', 'confirmed', 'shipping', 'completed', 'cancelled')
                                        NOT NULL DEFAULT 'pending',
    `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_orders_order_code` (`order_code`),
    KEY `idx_orders_user_id` (`user_id`),
    KEY `idx_orders_status` (`status`),
    KEY `idx_orders_created_at` (`created_at`),
    CONSTRAINT `fk_orders_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
-- 2.12. order_items - Chi tiết đơn hàng (lưu cứng tên/giá tại thời điểm mua)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_items` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `order_id`      INT UNSIGNED    NOT NULL,
    `product_id`    INT UNSIGNED    NOT NULL,
    `product_name`  VARCHAR(200)    NOT NULL,
    `price`         INT UNSIGNED    NOT NULL,
    `quantity`      INT UNSIGNED    NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_order_items_order_id` (`order_id`),
    KEY `idx_order_items_product_id` (`product_id`),
    CONSTRAINT `fk_order_items_order`
        FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_order_items_product`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
-- 2.13. posts - Bài viết / tin tức
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `posts` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `author_id`         INT UNSIGNED    NOT NULL,
    `title`             VARCHAR(255)    NOT NULL,
    `slug`              VARCHAR(280)    NOT NULL,
    `summary`           VARCHAR(500)    DEFAULT NULL,
    `content`           LONGTEXT        NOT NULL,
    `thumbnail`         VARCHAR(255)    DEFAULT NULL,
    `meta_title`        VARCHAR(255)    DEFAULT NULL,
    `meta_description`  VARCHAR(300)    DEFAULT NULL,
    `meta_keywords`     VARCHAR(255)    DEFAULT NULL,
    `status`            ENUM('draft', 'published')         NOT NULL DEFAULT 'draft',
    `views`             INT UNSIGNED    NOT NULL DEFAULT 0,
    `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_posts_slug` (`slug`),
    KEY `idx_posts_author_id` (`author_id`),
    KEY `idx_posts_status` (`status`),
    KEY `idx_posts_created_at` (`created_at`),
    KEY `idx_posts_title` (`title`),
    CONSTRAINT `fk_posts_author`
        FOREIGN KEY (`author_id`) REFERENCES `users` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
-- 2.14. comments - Bình luận bài viết
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comments` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `post_id`       INT UNSIGNED    NOT NULL,
    `user_id`       INT UNSIGNED    NOT NULL,
    `content`       TEXT            NOT NULL,
    `status`        ENUM('pending', 'approved', 'hidden') NOT NULL DEFAULT 'approved',
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_comments_post_id` (`post_id`),
    KEY `idx_comments_user_id` (`user_id`),
    KEY `idx_comments_status` (`status`),
    CONSTRAINT `fk_comments_post`
        FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_comments_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


-- ============================================================================
-- KẾT THÚC FILE
-- ============================================================================
