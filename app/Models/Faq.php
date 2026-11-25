<?php

declare(strict_types=1);

/**
 * FAQ công khai + quản trị. Chỉ dùng PDO qua Database::getInstance().
 * prepared statements, mỗi placeholder một tên, LIMIT/OFFSET/id dùng PARAM_INT.
 */
final class Faq
{
    private static function pdo(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    /** @return array<int, array<string, mixed>> */
    public static function getActive(): array
    {
        $stmt = self::pdo()->prepare(
            'SELECT id, question, answer, sort_order
             FROM faqs
             WHERE is_active = 1
             ORDER BY sort_order, id'
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * $isActive: null = tất cả, 1 = hiển thị, 0 = ẩn (whitelist).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function search(string $keyword = '', $isActive = null, int $limit = 10, int $offset = 0): array
    {
        $where = [];
        $params = [];
        if ($keyword !== '') {
            $where[] = '(question LIKE :kw1 OR answer LIKE :kw2)';
            $params[':kw1'] = '%' . $keyword . '%';
            $params[':kw2'] = '%' . $keyword . '%';
        }
        if (in_array($isActive, [1, 0, '1', '0'], true)) {
            $where[] = 'is_active = :is_active';
            $params[':is_active'] = (int) $isActive;
        }

        $sql = 'SELECT id, question, answer, sort_order, is_active FROM faqs';
        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY sort_order, id LIMIT :limit OFFSET :offset';

        $stmt = self::pdo()->prepare($sql);
        foreach ($params as $name => $value) {
            $stmt->bindValue($name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
        $stmt->bindValue(':offset', max(0, $offset), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function count(string $keyword = '', $isActive = null): int
    {
        $where = [];
        $params = [];
        if ($keyword !== '') {
            $where[] = '(question LIKE :kw1 OR answer LIKE :kw2)';
            $params[':kw1'] = '%' . $keyword . '%';
            $params[':kw2'] = '%' . $keyword . '%';
        }
        if (in_array($isActive, [1, 0, '1', '0'], true)) {
            $where[] = 'is_active = :is_active';
            $params[':is_active'] = (int) $isActive;
        }

        $sql = 'SELECT COUNT(*) FROM faqs';
        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

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
            'SELECT id, question, answer, sort_order, is_active FROM faqs WHERE id = :id'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row === false ? [] : $row;
    }

    /** @param array<string, mixed> $data question, answer, sort_order, is_active */
    public static function create(array $data): int
    {
        $stmt = self::pdo()->prepare(
            'INSERT INTO faqs (question, answer, sort_order, is_active)
             VALUES (:question, :answer, :sort_order, :is_active)'
        );
        $stmt->bindValue(':question', (string) $data['question'], PDO::PARAM_STR);
        $stmt->bindValue(':answer', (string) $data['answer'], PDO::PARAM_STR);
        $stmt->bindValue(':sort_order', (int) ($data['sort_order'] ?? 0), PDO::PARAM_INT);
        $stmt->bindValue(':is_active', !empty($data['is_active']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->execute();

        return (int) self::pdo()->lastInsertId();
    }

    /** @param array<string, mixed> $data question, answer, sort_order, is_active */
    public static function update(int $id, array $data): bool
    {
        $stmt = self::pdo()->prepare(
            'UPDATE faqs
             SET question = :question, answer = :answer,
                 sort_order = :sort_order, is_active = :is_active
             WHERE id = :id'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':question', (string) $data['question'], PDO::PARAM_STR);
        $stmt->bindValue(':answer', (string) $data['answer'], PDO::PARAM_STR);
        $stmt->bindValue(':sort_order', (int) ($data['sort_order'] ?? 0), PDO::PARAM_INT);
        $stmt->bindValue(':is_active', !empty($data['is_active']) ? 1 : 0, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function delete(int $id): bool
    {
        $stmt = self::pdo()->prepare('DELETE FROM faqs WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
