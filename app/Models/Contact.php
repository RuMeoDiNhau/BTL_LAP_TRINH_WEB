<?php

namespace App\Models;

/** Ghi liên hệ từ form FAQ vào bảng contacts (chỉ các cột có trong schema.sql). */
final class Contact
{
    /**
     * @param array<string, mixed> $data name, email, phone, subject, message, user_id
     */
    public static function create(array $data): bool
    {
        $stmt = \Database::pdo()->prepare(
            'INSERT INTO contacts (user_id, name, email, phone, subject, message, status)
             VALUES (:user_id, :name, :email, :phone, :subject, :message, :status)'
        );

        return $stmt->execute([
            ':user_id' => $data['user_id'] ?? null,
            ':name'    => (string)$data['name'],
            ':email'   => (string)$data['email'],
            ':phone'   => ($data['phone'] ?? '') === '' ? null : (string)$data['phone'],
            ':subject' => (string)$data['subject'],
            ':message' => (string)$data['message'],
            // ENUM('unread','read','replied') trong schema.sql; 'unread' = chưa đọc.
            ':status'  => 'unread',
        ]);
    }
}
