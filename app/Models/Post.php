<?php

declare(strict_types=1);

class Post
{
    private PDO $db;

    private const ALLOWED_STATUSES = ['draft', 'published'];

    public function __construct(?PDO $db = null)
    {
        if ($db instanceof PDO) {
            $this->db = $db;
            return;
        }

        $instance = Database::getInstance();
        $this->db = $instance instanceof PDO ? $instance : $instance->getConnection();
    }

    public function countPublished(string $keyword = ''): int
    {
        $keyword = trim($keyword);
        $sql = "SELECT COUNT(*) FROM `posts` WHERE `status` = 'published'";

        if ($keyword !== '') {
            $sql .= " AND (`title` LIKE :kw_title OR `summary` LIKE :kw_summary)";
        }

        $stmt = $this->db->prepare($sql);
        if ($keyword !== '') {
            $like = '%' . $keyword . '%';
            $stmt->bindValue(':kw_title', $like, PDO::PARAM_STR);
            $stmt->bindValue(':kw_summary', $like, PDO::PARAM_STR);
        }

        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function getPublished(string $keyword = '', int $limit = 6, int $offset = 0): array
    {
        $keyword = trim($keyword);
        $limit = max(1, $limit);
        $offset = max(0, $offset);

        $sql = "SELECT
                    p.`id`,
                    p.`author_id`,
                    p.`title`,
                    p.`slug`,
                    p.`summary`,
                    p.`thumbnail`,
                    p.`views`,
                    p.`created_at`,
                    u.`full_name` AS `author_name`
                FROM `posts` p
                INNER JOIN `users` u ON u.`id` = p.`author_id`
                WHERE p.`status` = 'published'";

        if ($keyword !== '') {
            $sql .= " AND (p.`title` LIKE :kw_title OR p.`summary` LIKE :kw_summary)";
        }

        $sql .= " ORDER BY p.`created_at` DESC, p.`id` DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        if ($keyword !== '') {
            $like = '%' . $keyword . '%';
            $stmt->bindValue(':kw_title', $like, PDO::PARAM_STR);
            $stmt->bindValue(':kw_summary', $like, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    }
