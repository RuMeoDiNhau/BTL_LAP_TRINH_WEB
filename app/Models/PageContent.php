<?php

namespace App\Models;

final class PageContent
{
    /**
     * Trả về mảng đã khóa theo section_key để view lấy trực tiếp.
     * Ví dụ: $sections['journey']['content'].
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getByPage(string $pageKey): array
    {
        $pageKey = self::pageKey($pageKey);
        $stmt = \Database::pdo()->prepare(
            'SELECT section_key, title, content, image, sort_order
             FROM page_contents
             WHERE page_key = :page_key
             ORDER BY sort_order, id'
        );
        $stmt->execute([':page_key' => $pageKey]);

        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[(string)$row['section_key']] = $row;
        }

        return $result;
    }

    /** Whitelist page_key: bảng dùng chung nên chỉ thao tác about/faq. */
    private static function pageKey(string $pageKey): string
    {
        if (!in_array($pageKey, ['about', 'faq'], true)) {
            throw new InvalidArgumentException('page_key không hợp lệ: ' . $pageKey);
        }

        return $pageKey;
    }

    /** Mọi khối của 1 trang cho trang quản lý. */
    public static function allByPage(string $pageKey): array
    {
        $stmt = \Database::pdo()->prepare(
            'SELECT id, page_key, section_key, title, content, image, sort_order, updated_at
             FROM page_contents
             WHERE page_key = :page_key
             ORDER BY id'
        );
        $stmt->execute([':page_key' => self::pageKey($pageKey)]);

        return $stmt->fetchAll();
    }

    /** @return array<string, mixed> [] nếu không tồn tại. */
    public static function find(string $pageKey, int $id): array
    {
        $stmt = \Database::pdo()->prepare(
            'SELECT id, page_key, section_key, title, content, image, sort_order, updated_at
             FROM page_contents
             WHERE page_key = :page_key AND id = :id'
        );
        $stmt->execute([':page_key' => self::pageKey($pageKey), ':id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? [] : $row;
    }

    /**
     * Lưu 1 khối; UNIQUE(page_key, section_key) nên chạy lại được.
     * $keepImage/$keepSortOrder cho phép giữ giá trị cũ hoặc set NULL rõ ràng (xoá ảnh).
     *
     * @param array<string, mixed> $data section_key, title, content, image, sort_order
     */
    public static function save(string $pageKey, array $data, bool $keepImage = false, bool $keepSortOrder = true): bool
    {
        $stmt = \Database::pdo()->prepare(
            'INSERT INTO page_contents
                (page_key, section_key, title, content, image, sort_order)
             VALUES
                (:page_key, :section_key, :title, :content, :image, :sort_order)
             ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                content = VALUES(content),
                image = IF(:keep_image = 1, image, VALUES(image)),
                sort_order = IF(:keep_sort = 1, sort_order, VALUES(sort_order))'
        );

        $image = self::nullable($data['image'] ?? null);
        $sortOrder = array_key_exists('sort_order', $data) ? (int)$data['sort_order'] : 0;

        return $stmt->execute([
            ':page_key'    => self::pageKey($pageKey),
            ':section_key' => (string)$data['section_key'],
            ':title'       => self::nullable($data['title'] ?? null),
            ':content'     => self::nullable($data['content'] ?? null),
            ':image'       => $image,
            ':sort_order'  => $sortOrder,
            ':keep_image'  => $keepImage ? 1 : 0,
            ':keep_sort'   => $keepSortOrder ? 1 : 0,
        ]);
    }

    /** Chuỗi rỗng xem như NULL để khớp cột DEFAULT NULL. */
    private static function nullable($value)
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string)$value);

        return $value === '' ? null : $value;
    }
}
