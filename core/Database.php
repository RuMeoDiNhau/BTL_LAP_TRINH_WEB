<?php

declare(strict_types=1);

/**
 * Singleton PDO dùng chung. Trả về object có getConnection(): PDO để các Model
 * của Thiện dùng được cả hai kiểu: $instance instanceof PDO ? ... : $instance->getConnection().
 * Core chỉ dùng cú pháp PHP >= 7.1 (?string, void), không dùng typed property/arrow fn.
 */
final class Database
{
    /** @var self|null */
    private static $instance = null;

    /** @var PDO|null */
    private static $pdo = null;

    /** @var array<string,mixed> */
    private static $config = [];

    /** Cho phép index.php nạp config sẵn; nếu không có tự đọc file. */
    public static function setConfig(array $config): void
    {
        self::$config = $config;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $cfg = self::$config;
        if ($cfg === []) {
            $root = dirname(__DIR__);
            $file = $root . '/config/database.php';
            if (!is_file($file)) {
                $file = $root . '/config/database.example.php';
            }
            $cfg = require $file;
            if (!is_array($cfg)) {
                $cfg = require $root . '/config/database.example.php';
            }
        }

        // Chấp nhận vài tên key thường gặp để tương thích config của từng người.
        $host    = (string) ($cfg['host'] ?? '127.0.0.1');
        $port    = (string) ($cfg['port'] ?? '3306');
        $dbname  = (string) ($cfg['dbname'] ?? $cfg['database'] ?? $cfg['name'] ?? 'decor_db');
        $user    = (string) ($cfg['username'] ?? $cfg['user'] ?? 'root');
        $pass    = (string) ($cfg['password'] ?? $cfg['pass'] ?? '');
        $charset = (string) ($cfg['charset'] ?? 'utf8mb4');

        $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $dbname . ';charset=' . $charset;
        self::$pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        return self::$pdo;
    }
}
