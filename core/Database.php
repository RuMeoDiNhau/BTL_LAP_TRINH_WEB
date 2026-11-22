<?php

/**
 * Kết nối PDO dùng chung. Chỉ đọc config/database.php (file local, không commit).
 * Mọi Model gọi Database::pdo() rồi prepare(); không nối chuỗi vào SQL.
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $configFile = dirname(__DIR__) . '/config/database.php';
            if (!is_file($configFile)) {
                throw new RuntimeException('Thiếu config/database.php. Copy từ config/database.example.php.');
            }

            /** @var array<string, mixed> $cfg */
            $cfg = require $configFile;

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $cfg['host'] ?? '127.0.0.1',
                $cfg['port'] ?? '3306',
                $cfg['dbname'] ?? 'decor_db',
                $cfg['charset'] ?? 'utf8mb4'
            );

            self::$pdo = new PDO(
                $dsn,
                (string)($cfg['username'] ?? ''),
                (string)($cfg['password'] ?? ''),
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        }

        return self::$pdo;
    }
}
