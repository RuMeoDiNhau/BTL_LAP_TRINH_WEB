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

    /** Bản ghi dành cho xác thực. findById() vẫn loại trừ password_hash. */
    public function findForAuthenticationById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, full_name, email, password_hash, phone, avatar, role, status FROM users WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public function emailExists(string $email, ?int $exceptId = null): bool
    {
        $email = mb_strtolower(trim($email), 'UTF-8');
        if ($email === '') {
            return false;
        }

        $sql = "SELECT COUNT(*) FROM `users` WHERE `email` = :email";
        if ($exceptId !== null) {
            $sql .= ' AND id <> :except_id';
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        if ($exceptId !== null) {
            $stmt->bindValue(':except_id', $exceptId, PDO::PARAM_INT);
        }
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

    /** Hồ sơ cá nhân chỉ cập nhật các cột được phép. */
    public function updateProfile(int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE users SET full_name = :full_name, email = :email, phone = :phone WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':full_name', $data['full_name'], PDO::PARAM_STR);
        $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
        $phone = $data['phone'] === '' ? null : $data['phone'];
        $stmt->bindValue(':phone', $phone, $phone === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->execute();
    }

    public function updatePassword(int $id, string $oldHash, string $newHash): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = :new_hash WHERE id = :id AND password_hash = :old_hash');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':old_hash', $oldHash, PDO::PARAM_STR);
        $stmt->bindValue(':new_hash', $newHash, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }

    public function updateAvatar(int $id, ?string $previous, ?string $path): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET avatar = :avatar WHERE id = :id AND avatar <=> :previous');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':avatar', $path, $path === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':previous', $previous, $previous === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }
}
