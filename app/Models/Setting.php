<?php

declare(strict_types=1);

class Setting
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        if ($db instanceof PDO) {
            $this->db = $db;
            return;
        }

        $connection = Database::getInstance();
        $this->db = $connection instanceof PDO ? $connection : $connection->getConnection();
    }

    /** @return array<string,string|null> */
    public function all(): array
    {
        $stmt = $this->db->query('SELECT setting_key, setting_value FROM settings');
        $settings = [];
        foreach ($stmt->fetchAll() as $row) {
            $settings[(string) $row['setting_key']] = $row['setting_value'] === null
                ? null
                : (string) $row['setting_value'];
        }
        return $settings;
    }

    /** @param array<string,string|null> $values */
    public function save(array $values): void
    {
        $allowed = ['site_name', 'phone', 'email', 'address', 'logo'];
        $stmt = $this->db->prepare(
            'INSERT INTO settings (setting_key, setting_value) VALUES (:setting_key, :setting_value) '
            . 'ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
        );

        foreach ($values as $key => $value) {
            if (!in_array($key, $allowed, true)) {
                continue;
            }
            $stmt->bindValue(':setting_key', $key, PDO::PARAM_STR);
            $stmt->bindValue(':setting_value', $value, $value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->execute();
        }
    }
}
