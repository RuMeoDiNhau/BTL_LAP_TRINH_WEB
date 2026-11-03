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
    public function findPublishedBySlug(string $slug): ?array
    {
        $slug = trim($slug);
        if ($slug === '') {
            return null;
        }

        $sql = "SELECT
                    p.*,
                    u.`full_name` AS `author_name`,
                    u.`avatar` AS `author_avatar`
                FROM `posts` p
                INNER JOIN `users` u ON u.`id` = p.`author_id`
                WHERE p.`slug` = :slug
                  AND p.`status` = 'published'
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public function incrementViews(int $id): bool
    {
        $sql = "UPDATE `posts` SET `views` = `views` + 1 WHERE `id` = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function adminCount(string $keyword = '', ?string $status = null): int
    {
        $keyword = trim($keyword);
        $sql = "SELECT COUNT(*) FROM `posts` p WHERE 1 = 1";

        if ($keyword !== '') {
            $sql .= " AND (p.`title` LIKE :kw_title OR p.`slug` LIKE :kw_slug)";
        }

        if ($status !== null && in_array($status, self::ALLOWED_STATUSES, true)) {
            $sql .= " AND p.`status` = :status";
        }

        $stmt = $this->db->prepare($sql);

        if ($keyword !== '') {
            $like = '%' . $keyword . '%';
            $stmt->bindValue(':kw_title', $like, PDO::PARAM_STR);
            $stmt->bindValue(':kw_slug', $like, PDO::PARAM_STR);
        }

        if ($status !== null && in_array($status, self::ALLOWED_STATUSES, true)) {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }

        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function adminPaginate(
        string $keyword = '',
        ?string $status = null,
        int $limit = 10,
        int $offset = 0
    ): array {
        $keyword = trim($keyword);
        $limit = max(1, $limit);
        $offset = max(0, $offset);

        $sql = "SELECT
                    p.`id`,
                    p.`author_id`,
                    p.`title`,
                    p.`slug`,
                    p.`thumbnail`,
                    p.`status`,
                    p.`views`,
                    p.`created_at`,
                    u.`full_name` AS `author_name`
                FROM `posts` p
                INNER JOIN `users` u ON u.`id` = p.`author_id`
                WHERE 1 = 1";

        if ($keyword !== '') {
            $sql .= " AND (p.`title` LIKE :kw_title OR p.`slug` LIKE :kw_slug)";
        }

        if ($status !== null && in_array($status, self::ALLOWED_STATUSES, true)) {
            $sql .= " AND p.`status` = :status";
        }

        $sql .= " ORDER BY p.`created_at` DESC, p.`id` DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        if ($keyword !== '') {
            $like = '%' . $keyword . '%';
            $stmt->bindValue(':kw_title', $like, PDO::PARAM_STR);
            $stmt->bindValue(':kw_slug', $like, PDO::PARAM_STR);
        }

        if ($status !== null && in_array($status, self::ALLOWED_STATUSES, true)) {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM `posts` WHERE `id` = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM `posts` WHERE `slug` = :slug";
        if ($ignoreId !== null) {
            $sql .= " AND `id` != :ignore_id";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':slug', trim($slug), PDO::PARAM_STR);
        if ($ignoreId !== null) {
            $stmt->bindValue(':ignore_id', $ignoreId, PDO::PARAM_INT);
        }

        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $data): int
    {
        $status = in_array($data['status'] ?? 'draft', self::ALLOWED_STATUSES, true)
            ? (string) $data['status']
            : 'draft';

        $sql = "INSERT INTO `posts` (
                    `author_id`,
                    `title`,
                    `slug`,
                    `summary`,
                    `content`,
                    `thumbnail`,
                    `meta_title`,
                    `meta_description`,
                    `meta_keywords`,
                    `status`
                ) VALUES (
                    :author_id,
                    :title,
                    :slug,
                    :summary,
                    :content,
                    :thumbnail,
                    :meta_title,
                    :meta_description,
                    :meta_keywords,
                    :status
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':author_id', (int) $data['author_id'], PDO::PARAM_INT);
        $stmt->bindValue(':title', trim((string) $data['title']), PDO::PARAM_STR);
        $stmt->bindValue(':slug', trim((string) $data['slug']), PDO::PARAM_STR);
        $stmt->bindValue(':summary', self::nullableString($data['summary'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(':content', (string) $data['content'], PDO::PARAM_STR);
        $stmt->bindValue(':thumbnail', self::nullableString($data['thumbnail'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(':meta_title', self::nullableString($data['meta_title'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(':meta_description', self::nullableString($data['meta_description'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(':meta_keywords', self::nullableString($data['meta_keywords'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);

        $stmt->execute();
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $status = in_array($data['status'] ?? 'draft', self::ALLOWED_STATUSES, true)
            ? (string) $data['status']
            : 'draft';

        $sql = "UPDATE `posts`
                SET `title`            = :title,
                    `slug`             = :slug,
                    `summary`          = :summary,
                    `content`          = :content,
                    `thumbnail`        = :thumbnail,
                    `meta_title`       = :meta_title,
                    `meta_description` = :meta_description,
                    `meta_keywords`    = :meta_keywords,
                    `status`           = :status
                WHERE `id` = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':title', trim((string) $data['title']), PDO::PARAM_STR);
        $stmt->bindValue(':slug', trim((string) $data['slug']), PDO::PARAM_STR);
        $stmt->bindValue(':summary', self::nullableString($data['summary'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(':content', (string) $data['content'], PDO::PARAM_STR);
        $stmt->bindValue(':thumbnail', self::nullableString($data['thumbnail'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(':meta_title', self::nullableString($data['meta_title'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(':meta_description', self::nullableString($data['meta_description'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(':meta_keywords', self::nullableString($data['meta_keywords'] ?? null), PDO::PARAM_STR);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM `posts` WHERE `id` = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function generateSlug(string $title): string
    {
        $str = mb_strtolower(trim($title), 'UTF-8');

        $vietnameseMap = [
            '/[àáạảãâầấậẩẫăằắặẳẵ]/u' => 'a',
            '/[èéẹẻẽêềếệểễ]/u'       => 'e',
            '/[ìíịỉĩ]/u'             => 'i',
            '/[òóọỏõôồốộổỗơờớợởỡ]/u' => 'o',
            '/[ùúụủũưừứựửữ]/u'       => 'u',
            '/[ỳýỵỷỹ]/u'             => 'y',
            '/đ/u'                   => 'd',
        ];

        $str = preg_replace(array_keys($vietnameseMap), array_values($vietnameseMap), $str) ?? $str;
        $str = preg_replace('/[^a-z0-9\s-]/', '', $str) ?? $str;
        $str = preg_replace('/[\s-]+/', '-', $str) ?? $str;

        return trim($str, '-');
    }

    private static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $trimmed = trim((string) $value);
        return $trimmed === '' ? null : $trimmed;
    }
    }
