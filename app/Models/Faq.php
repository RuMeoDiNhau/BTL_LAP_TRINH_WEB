<?php

namespace App\Models;

use PDO;

final class Faq
{
    /** @return array<int, array<string, mixed>> */
    public static function getActive(): array
    {
        $stmt = \Database::pdo()->prepare(
            'SELECT id, question, answer, sort_order
             FROM faqs
             WHERE is_active = 1
             ORDER BY sort_order, id'
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Danh sách admin: tìm theo câu hỏi/đáp án + lọc trạng thái + phân trang.
     * $isActive: null = tất cả, 1 = hiển thị, 0 = ẩn.
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
        if ($isActive === 1 || $isActive === 0 || $isActive === '1' || $isActive === '0') {
            $where[] = 'is_active = :is_active';
            $params[':is_active'] = (int)$isActive;
        }
        $sql = 'SELECT id, question, answer, sort_order, is_active FROM faqs';
        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY sort_order, id LIMIT :limit OFFSET :offset';

        $stmt = \Database::pdo()->prepare($sql);
        foreach ($params as $name => $value) {
            $stmt->bindValue($name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
        $stmt->bindValue(':offset', max(0, $offset), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** Tổng số dòng theo từ khóa + trạng thái (để làm phân trang). */
    public static function count(string $keyword = '', $isActive = null): int
    {
        $where = [];
        $params = [];
        if ($keyword !== '') {
            $where[] = '(question LIKE :kw1 OR answer LIKE :kw2)';
            $params[':kw1'] = '%' . $keyword . '%';
            $params[':kw2'] = '%' . $keyword . '%';
        }
        if ($isActive === 1 || $isActive === 0 || $isActive === '1' || $isActive === '0') {
            $where[] = 'is_active = :is_active';
            $params[':is_active'] = (int)$isActive;
        }
        $sql = 'SELECT COUNT(*) FROM faqs';
        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

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
            'SELECT id, question, answer, sort_order, is_active FROM faqs WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? [] : $row;
    }

    /**
     * @param array<string, mixed> $data question, answer, sort_order, is_active
     */
    public static function create(array $data): int
    {
        $stmt = \Database::pdo()->prepare(
            'INSERT INTO faqs (question, answer, sort_order, is_active)
             VALUES (:question, :answer, :sort_order, :is_active)'
        );
        $stmt->execute([
            ':question'   => (string)$data['question'],
            ':answer'     => (string)$data['answer'],
            ':sort_order' => (int)($data['sort_order'] ?? 0),
            ':is_active'  => !empty($data['is_active']) ? 1 : 0,
        ]);

        return (int)\Database::pdo()->lastInsertId();
    }

    /**
     * @param array<string, mixed> $data question, answer, sort_order, is_active
     */
    public static function update(int $id, array $data): bool
    {
        $stmt = \Database::pdo()->prepare(
            'UPDATE faqs
             SET question = :question, answer = :answer,
                 sort_order = :sort_order, is_active = :is_active
             WHERE id = :id'
        );

        return $stmt->execute([
            ':id'         => $id,
            ':question'   => (string)$data['question'],
            ':answer'     => (string)$data['answer'],
            ':sort_order' => (int)($data['sort_order'] ?? 0),
            ':is_active'  => !empty($data['is_active']) ? 1 : 0,
        ]);
    }

    public static function delete(int $id): bool
    {
        $stmt = \Database::pdo()->prepare('DELETE FROM faqs WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }
}
