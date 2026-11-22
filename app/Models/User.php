<?php

declare(strict_types=1);

class User
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        if ($db instanceof PDO) {
            $this->db = $db;
            return;
        }

        $instance = Database::getInstance();
        $this->db = $instance instanceof PDO ? $instance : $instance->getConnection();
    }

    public function findByEmail(string $email): ?array
    {
        $email = mb_strtolower(trim($email), 'UTF-8');
        if ($email === '') {
            return null;
        }

        $sql = "SELECT `id`, `full_name`, `email`, `password_hash`, `phone`, `avatar`, `role`, `status`, `created_at`
                FROM `users`
                WHERE `email` = :email
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT `id`, `full_name`, `email`, `phone`, `avatar`, `role`, `status`, `created_at`
                FROM `users`
                WHERE `id` = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public function emailExists(string $email): bool
    {
        $email = mb_strtolower(trim($email), 'UTF-8');
        if ($email === '') {
            return false;
        }

        $sql = "SELECT COUNT(*) FROM `users` WHERE `email` = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    public function createMember(string $fullName, string $email, string $passwordHash): int
    {
        $sql = "INSERT INTO `users` (`full_name`, `email`, `password_hash`, `role`, `status`)
                VALUES (:full_name, :email, :password_hash, 'member', 'active')";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':full_name', trim($fullName), PDO::PARAM_STR);
        $stmt->bindValue(':email', mb_strtolower(trim($email), 'UTF-8'), PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->execute();

        return (int) $this->db->lastInsertId();
    }

    public function countByRole(): array
    {
        $sql = "SELECT `role`, COUNT(*) AS `total` FROM `users` GROUP BY `role`";
        $stmt = $this->db->query($sql);

        $counts = [
            'admin'  => 0,
            'member' => 0,
        ];

        if ($stmt !== false) {
            foreach ($stmt->fetchAll() as $row) {
                $role = (string) ($row['role'] ?? '');
                if (array_key_exists($role, $counts)) {
                    $counts[$role] = (int) $row['total'];
                }
            }
        }

        return $counts;
    }
}
