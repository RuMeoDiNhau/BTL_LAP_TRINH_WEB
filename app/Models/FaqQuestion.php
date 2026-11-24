<?php

declare(strict_types=1);

/**
 * Câu hỏi gửi từ trang FAQ: chỉ làm việc với contacts có source='faq'.
 * Tách riêng model Contact của phần #1, không đụng dòng source='contact'.
 */
final class FaqQuestion
{
    private const SOURCE = 'faq';

    private static function pdo(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    /** @param array<string, mixed> $data name, email, phone, subject, message, user_id */
    public static function create(array $data): bool
    {
        $stmt = self::pdo()->prepare(
            'INSERT INTO contacts (user_id, name, email, phone, subject, message, status, source)
             VALUES (:user_id, :name, :email, :phone, :subject, :message, :status, :source)'
        );
        $userId = isset($data['user_id']) ? (int) $data['user_id'] : 0;
        $stmt->bindValue(':user_id', $userId > 0 ? $userId : null, $userId > 0 ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':name', (string) $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':email', (string) $data['email'], PDO::PARAM_STR);
        $phone = (string) ($data['phone'] ?? '');
        $stmt->bindValue(':phone', $phone === '' ? null : $phone, $phone === '' ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':subject', (string) $data['subject'], PDO::PARAM_STR);
        $stmt->bindValue(':message', (string) $data['message'], PDO::PARAM_STR);
        $stmt->bindValue(':status', 'unread', PDO::PARAM_STR);
        $stmt->bindValue(':source', self::SOURCE, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /** @return array<int, array<string, mixed>> */
    public static function search(string $keyword = '', $status = null, int $limit = 10, int $offset = 0): array
    {
        $where = ['source = :source'];
        $params = [':source' => self::SOURCE];
        self::appendFilters($where, $params, $keyword, $status);

        $sql = 'SELECT id, name, email, phone, subject, message, status, created_at FROM contacts'
             . ' WHERE ' . implode(' AND ', $where)
             . ' ORDER BY created_at DESC, id DESC LIMIT :limit OFFSET :offset';

        $stmt = self::pdo()->prepare($sql);
        foreach ($params as $name => $value) {
            $stmt->bindValue($name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
        $stmt->bindValue(':offset', max(0, $offset), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function count(string $keyword = '', $status = null): int
    {
        $where = ['source = :source'];
        $params = [':source' => self::SOURCE];
        self::appendFilters($where, $params, $keyword, $status);

        $sql = 'SELECT COUNT(*) FROM contacts WHERE ' . implode(' AND ', $where);
        $stmt = self::pdo()->prepare($sql);
        foreach ($params as $name => $value) {
            $stmt->bindValue($name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /** @return array<string, mixed> [] nếu không tồn tại. */
    public static function find(int $id): array
    {
        $stmt = self::pdo()->prepare(
            'SELECT id, name, email, phone, subject, message, status, created_at
             FROM contacts WHERE id = :id AND source = :source'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':source', self::SOURCE, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row === false ? [] : $row;
    }

    /** Chỉ đổi sang trạng thái hợp lệ (whitelist ENUM). */
    public static function setStatus(int $id, string $status): bool
    {
        if (!self::isStatus($status)) {
            return false;
        }
        $stmt = self::pdo()->prepare(
            'UPDATE contacts SET status = :status WHERE id = :id AND source = :source'
        );
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':source', self::SOURCE, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function delete(int $id): bool
    {
        $stmt = self::pdo()->prepare('DELETE FROM contacts WHERE id = :id AND source = :source');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':source', self::SOURCE, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function countUnread(): int
    {
        $stmt = self::pdo()->prepare(
            'SELECT COUNT(*) FROM contacts WHERE source = :source AND status = :status'
        );
        $stmt->bindValue(':source', self::SOURCE, PDO::PARAM_STR);
        $stmt->bindValue(':status', 'unread', PDO::PARAM_STR);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * @param array<int, string> $where
     * @param array<string, mixed> $params
     */
    private static function appendFilters(array &$where, array &$params, string $keyword, $status): void
    {
        if ($keyword !== '') {
            // Mỗi cột một tham số riêng, không nối chuỗi vào SQL.
            $where[] = '(name LIKE :kw1 OR email LIKE :kw2 OR subject LIKE :kw3 OR message LIKE :kw4)';
            $like = '%' . $keyword . '%';
            $params[':kw1'] = $like;
            $params[':kw2'] = $like;
            $params[':kw3'] = $like;
            $params[':kw4'] = $like;
        }
        if (self::isStatus($status)) {
            $where[] = 'status = :status';
            $params[':status'] = (string) $status;
        }
    }

    private static function isStatus($status): bool
    {
        return in_array($status, ['unread', 'read', 'replied'], true);
    }
}
