<?php

declare(strict_types=1);

class User
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getConnection();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function emailExists(string $email, int $exceptId = 0): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1');
        $stmt->execute(['email' => $email, 'id' => $exceptId]);
        return $stmt->fetchColumn() !== false;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO users (full_name, email, password_hash, role, status) VALUES (:full_name, :email, :password_hash, 'member', 'active')");
        $stmt->execute([
            'full_name' => $data['full_name'], 'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
        ]);
        return (int) $this->db->lastInsertId();
    }

    /** Whitelist cột: form hồ sơ không thể tự đổi role, status hoặc mật khẩu. */
    public function updateProfile(int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE users SET full_name = :full_name, email = :email, phone = :phone WHERE id = :id');
        $stmt->execute(['id' => $id, 'full_name' => $data['full_name'], 'email' => $data['email'], 'phone' => $data['phone'] ?: null]);
    }

    public function updatePassword(int $id, string $oldHash, string $newHash): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = :new_hash WHERE id = :id AND password_hash = :old_hash');
        $stmt->execute(['id' => $id, 'new_hash' => $newHash, 'old_hash' => $oldHash]);
        return $stmt->rowCount() === 1;
    }

    public function updateAvatar(int $id, ?string $path): void
    {
        $stmt = $this->db->prepare('UPDATE users SET avatar = :avatar WHERE id = :id');
        $stmt->execute(['id' => $id, 'avatar' => $path]);
    }
}
