<?php

namespace App\Models;

/**
 * Minimal post reader for the About page blog strip.
 * Schema note: posts has `summary` (not "excerpt") and `created_at`
 * (not "published_at"); both are mapped to the keys the view expects.
 * There is no post-category column, so category_name uses a display fallback.
 */
final class Post
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function getLatest(int $limit = 3): array
    {
        // Ép giới hạn về int trước khi bind để LIMIT không bị chèn chuỗi.
        $limit = max(1, min(100, $limit));

        $stmt = \Database::pdo()->prepare(
            'SELECT p.id, p.title, p.slug, p.summary, p.content, p.thumbnail,
                    p.created_at AS published_at
             FROM posts p
             WHERE p.status = :status
             ORDER BY p.created_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':status', 'published', \PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        $posts = [];
        foreach ($stmt->fetchAll() as $row) {
            $excerpt = trim((string)($row['summary'] ?? ''));
            if ($excerpt === '') {
                $excerpt = mb_strimwidth(
                    strip_tags((string)($row['content'] ?? '')),
                    0,
                    120,
                    '…',
                    'UTF-8'
                );
            }

            $posts[] = [
                'id'            => (int)$row['id'],
                'title'         => (string)$row['title'],
                'slug'          => (string)$row['slug'],
                'excerpt'       => $excerpt,
                'thumbnail'     => (string)($row['thumbnail'] ?? ''),
                'category_name' => 'News', // schema has no post category relation
                'published_at'  => (string)($row['published_at'] ?? ''),
            ];
        }

        return $posts;
    }
}
