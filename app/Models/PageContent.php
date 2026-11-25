<?php

declare(strict_types=1);

/**
 * Nội dung trang về/FAQ. page_key luôn đi qua whitelist vì bảng dùng chung.
 * Lưu bằng UNIQUE(page_key, section_key) + ON DUPLICATE KEY UPDATE.
 */
final class PageContent
{
    private static function pdo(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    /**
     * Trả về mảng khóa theo section_key: $sections['journey']['content'].
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getByPage(string $pageKey): array
    {
        $stmt = self::pdo()->prepare(
            'SELECT section_key, title, content, image, sort_order
             FROM page_contents
             WHERE page_key = :page_key
             ORDER BY sort_order, id'
        );
        $stmt->bindValue(':page_key', self::pageKey($pageKey), PDO::PARAM_STR);
        $stmt->execute();

        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[(string) $row['section_key']] = $row;
        }

        return $result;
    }

    /** @return array<int, array<string, mixed>> */
    public static function allByPage(string $pageKey): array
    {
        $stmt = self::pdo()->prepare(
            'SELECT id, page_key, section_key, title, content, image, sort_order, updated_at
             FROM page_contents
             WHERE page_key = :page_key
             ORDER BY id'
        );
        $stmt->bindValue(':page_key', self::pageKey($pageKey), PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** @return array<string, mixed> [] nếu không tồn tại. */
    public static function find(string $pageKey, int $id): array
    {
        $stmt = self::pdo()->prepare(
            'SELECT id, page_key, section_key, title, content, image, sort_order, updated_at
             FROM page_contents
             WHERE page_key = :page_key AND id = :id'
        );
        $stmt->bindValue(':page_key', self::pageKey($pageKey), PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row === false ? [] : $row;
    }

    /**
     * $keepImage/$keepSortOrder: giữ giá trị cũ thay vì ghi đè.
     * Không upload ảnh mới -> controller truyền keepImage=true.
     *
     * @param array<string, mixed> $data section_key, title, content, image, sort_order
     */
    public static function save(string $pageKey, array $data, bool $keepImage = false, bool $keepSortOrder = true): bool
    {
        $stmt = self::pdo()->prepare(
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

        $sortOrder = array_key_exists('sort_order', $data) ? (int) $data['sort_order'] : 0;
        $stmt->bindValue(':page_key', self::pageKey($pageKey), PDO::PARAM_STR);
        $stmt->bindValue(':section_key', (string) $data['section_key'], PDO::PARAM_STR);
        $title = self::nullable($data['title'] ?? null);
        $content = self::nullable($data['content'] ?? null);
        $image = self::nullable($data['image'] ?? null);
        $stmt->bindValue(':title', $title, $title === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':content', $content, $content === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':image', $image, $image === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':sort_order', $sortOrder, PDO::PARAM_INT);
        // Hai placeholder riêng: EMULATE_PREPARES=false không cho dùng lại cùng tên.
        $stmt->bindValue(':keep_image', $keepImage ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':keep_sort', $keepSortOrder ? 1 : 0, PDO::PARAM_INT);

        return $stmt->execute();
    }

    private static function pageKey(string $pageKey): string
    {
        if (!in_array($pageKey, ['about', 'faq'], true)) {
            throw new InvalidArgumentException('page_key không hợp lệ: ' . $pageKey);
        }

        return $pageKey;
    }

    /** Chuỗi rỗng xem như NULL để khớp cột DEFAULT NULL. */
    private static function nullable($value)
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
