<?php

namespace App\Models;

use PDO;

/**
 * Câu hỏi gửi từ trang FAQ: chỉ làm việc với các dòng contacts có source='faq'.
 * Không dùng lại model Contact của phần #1 để tránh đụng tin nhắn từ trang Liên hệ.
 */
final class FaqQuestion
{
    private const SOURCE = 'faq';

    /** @param array<string, mixed> $data name, email, phone, subject, message, user_id */
    public static function create(array $data): bool
    {
        $stmt = \Database::pdo()->prepare(
            'INSERT INTO contacts (user_id, name, email, phone, subject, message, status, source)
             VALUES (:user_id, :name, :email, :phone, :subject, :message, :status, :source)'
        );

        return $stmt->execute([
            ':user_id' => isset($data['user_id']) && (int)$data['user_id'] > 0 ? (int)$data['user_id'] : null,
            ':name'    => (string)$data['name'],
            ':email'   => (string)$data['email'],
            ':phone'   => (($data['phone'] ?? '') === '') ? null : (string)$data['phone'],
            ':subject' => (string)$data['subject'],
            ':message' => (string)$data['message'],
            // Schema mặc định 'unread'; ghi rõ để không phụ thuộc vào DEFAULT.
            ':status'  => 'unread',
            ':source'  => self::SOURCE,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function search(string $keyword = '', $status = null, int $limit = 10, int $offset = 0): array
    {
        $where = ['source = :source'];
        $params = [':source' => self::SOURCE];
        self::appendFilters($where, $params, $keyword, $status);

        $sql = 'SELECT id, name, email, phone, subject, message, status, created_at FROM contacts'
             . ' WHERE ' . implode(' AND ', $where)
             . ' ORDER BY created_at DESC, id DESC LIMIT :limit OFFSET :offset';

        $stmt = \Database::pdo()->prepare($sql);
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
        $stmt = \Database::pdo()->prepare($sql);
        foreach ($params as $name => $value) {
            $stmt->bindValue($name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    /** @return array<string, mixed> [] nếu không tồn tại. */
    public static function find(int $id): array
    {
        $stmt = \Database::pdo()->prepare(
            'SELECT id, name, email, phone, subject, message, status, created_at
             FROM contacts WHERE id = :id AND source = :source'
        );
        $stmt->execute([':id' => $id, ':source' => self::SOURCE]);
        $row = $stmt->fetch();

        return $row === false ? [] : $row;
    }

    /** Chỉ đổi sang các trạng thái hợp lệ; không bao giờ đụng dòng source='contact'. */
    public static function setStatus(int $id, string $status): bool
    {
        if (!self::isStatus($status)) {
            return false;
        }
        $stmt = \Database::pdo()->prepare(
            'UPDATE contacts SET status = :status WHERE id = :id AND source = :source'
        );

        return $stmt->execute([':id' => $id, ':status' => $status, ':source' => self::SOURCE]);
    }

    public static function delete(int $id): bool
    {
        $stmt = \Database::pdo()->prepare('DELETE FROM contacts WHERE id = :id AND source = :source');

        return $stmt->execute([':id' => $id, ':source' => self::SOURCE]);
    }

    public static function countUnread(): int
    {
        $stmt = \Database::pdo()->prepare(
            'SELECT COUNT(*) FROM contacts WHERE source = :source AND status = :status'
        );
        $stmt->execute([':source' => self::SOURCE, ':status' => 'unread']);

        return (int)$stmt->fetchColumn();
    }

    /**
     * @param array<int, string> $where
     * @param array<string, mixed> $params
     */
    private static function appendFilters(array &$where, array &$params, string $keyword, $status): void
    {
        if ($keyword !== '') {
            // Mỗi cột một tham số riêng (:kw1..:kw4), không nối chuỗi vào SQL.
            $where[] = '(name LIKE :kw1 OR email LIKE :kw2 OR subject LIKE :kw3 OR message LIKE :kw4)';
            $params[':kw1'] = '%' . $keyword . '%';
            $params[':kw2'] = '%' . $keyword . '%';
            $params[':kw3'] = '%' . $keyword . '%';
            $params[':kw4'] = '%' . $keyword . '%';
        }
        if (self::isStatus($status)) {
            $where[] = 'status = :status';
            $params[':status'] = (string)$status;
        }
    }

    private static function isStatus($status): bool
    {
        return in_array($status, ['unread', 'read', 'replied'], true);
    }
}
