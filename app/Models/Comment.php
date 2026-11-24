<?php

declare(strict_types=1);

class Comment
{
    private PDO $db;

    private const ALLOWED_STATUSES = ['pending', 'approved', 'hidden'];

    public function __construct(?PDO $db = null)
    {
        if ($db instanceof PDO) {
            $this->db = $db;
            return;
        }

        $instance = Database::getInstance();
        $this->db = $instance instanceof PDO ? $instance : $instance->getConnection();
    }

    public function countApprovedByPost(int $postId): int
    {
        $sql = "SELECT COUNT(*) FROM `comments` WHERE `post_id` = :post_id AND `status` = 'approved'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function getApprovedByPost(int $postId, int $limit = 10, int $offset = 0): array
    {
        $limit = max(1, $limit);
        $offset = max(0, $offset);

        $sql = "SELECT
                    c.`id`,
                    c.`post_id`,
                    c.`user_id`,
                    c.`content`,
                    c.`status`,
                    c.`created_at`,
                    u.`full_name` AS `author_name`,
                    u.`avatar` AS `author_avatar`
                FROM `comments` c
                INNER JOIN `users` u ON u.`id` = c.`user_id`
                WHERE c.`post_id` = :post_id
                  AND c.`status` = 'approved'
                ORDER BY c.`created_at` DESC, c.`id` DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function create(int $postId, int $userId, string $content, string $status = 'approved'): int
    {
        $status = in_array($status, self::ALLOWED_STATUSES, true) ? $status : 'approved';

        $sql = "INSERT INTO `comments` (`post_id`, `user_id`, `content`, `status`)
                VALUES (:post_id, :user_id, :content, :status)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':content', trim($content), PDO::PARAM_STR);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->execute();

        return (int) $this->db->lastInsertId();
    }

    public function adminCount(?string $status = null, ?int $postId = null, string $keyword = ''): int
    {
        $keyword = trim($keyword);
        $sql = "SELECT COUNT(*)
                FROM `comments` c
                INNER JOIN `users` u ON u.`id` = c.`user_id`
                INNER JOIN `posts` p ON p.`id` = c.`post_id`
                WHERE 1 = 1";

        if ($status !== null && in_array($status, self::ALLOWED_STATUSES, true)) {
            $sql .= " AND c.`status` = :status";
        }
        if ($postId !== null && $postId > 0) {
            $sql .= " AND c.`post_id` = :post_id";
        }
        if ($keyword !== '') {
            $sql .= " AND (c.`content` LIKE :kw_content OR u.`full_name` LIKE :kw_user OR p.`title` LIKE :kw_post)";
        }

        $stmt = $this->db->prepare($sql);

        if ($status !== null && in_array($status, self::ALLOWED_STATUSES, true)) {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }
        if ($postId !== null && $postId > 0) {
            $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        }
        if ($keyword !== '') {
            $like = '%' . $keyword . '%';
            $stmt->bindValue(':kw_content', $like, PDO::PARAM_STR);
            $stmt->bindValue(':kw_user', $like, PDO::PARAM_STR);
            $stmt->bindValue(':kw_post', $like, PDO::PARAM_STR);
        }

        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function adminPaginate(
        ?string $status = null,
        ?int $postId = null,
        string $keyword = '',
        int $limit = 10,
        int $offset = 0
    ): array {
        $keyword = trim($keyword);
        $limit = max(1, $limit);
        $offset = max(0, $offset);

        $sql = "SELECT
                    c.`id`,
                    c.`post_id`,
                    c.`user_id`,
                    c.`content`,
                    c.`status`,
                    c.`created_at`,
                    u.`full_name` AS `author_name`,
                    u.`email` AS `author_email`,
                    p.`title` AS `post_title`,
                    p.`slug` AS `post_slug`
                FROM `comments` c
                INNER JOIN `users` u ON u.`id` = c.`user_id`
                INNER JOIN `posts` p ON p.`id` = c.`post_id`
                WHERE 1 = 1";

        if ($status !== null && in_array($status, self::ALLOWED_STATUSES, true)) {
            $sql .= " AND c.`status` = :status";
        }
        if ($postId !== null && $postId > 0) {
            $sql .= " AND c.`post_id` = :post_id";
        }
        if ($keyword !== '') {
            $sql .= " AND (c.`content` LIKE :kw_content OR u.`full_name` LIKE :kw_user OR p.`title` LIKE :kw_post)";
        }

        $sql .= " ORDER BY c.`created_at` DESC, c.`id` DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        if ($status !== null && in_array($status, self::ALLOWED_STATUSES, true)) {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }
        if ($postId !== null && $postId > 0) {
            $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        }
        if ($keyword !== '') {
            $like = '%' . $keyword . '%';
            $stmt->bindValue(':kw_content', $like, PDO::PARAM_STR);
            $stmt->bindValue(':kw_user', $like, PDO::PARAM_STR);
            $stmt->bindValue(':kw_post', $like, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, self::ALLOWED_STATUSES, true)) {
            return false;
        }

        $sql = "UPDATE `comments` SET `status` = :status WHERE `id` = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM `comments` WHERE `id` = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function isModerationEnabled(): bool
    {
        $sql = "SELECT `setting_value` FROM `settings` WHERE `setting_key` = 'comment_moderation' LIMIT 1";
        $stmt = $this->db->query($sql);
        $val = $stmt !== false ? $stmt->fetchColumn() : false;

        return $val === '1';
    }

    public function setModerationEnabled(bool $enabled): bool
    {
        $value = $enabled ? '1' : '0';
        $sql = "INSERT INTO `settings` (`setting_key`, `setting_value`)
                VALUES ('comment_moderation', :val_insert)
                ON DUPLICATE KEY UPDATE `setting_value` = :val_update";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':val_insert', $value, PDO::PARAM_STR);
        $stmt->bindValue(':val_update', $value, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
