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
}
