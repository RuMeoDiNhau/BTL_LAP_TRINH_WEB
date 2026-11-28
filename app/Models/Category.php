<?php

declare(strict_types=1);

final class Category
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getConnection();
    }

    public function all(): array
    {
        return $this->db->query('SELECT id, name FROM categories ORDER BY name, id')->fetchAll();
    }

    public function exists(int $id): bool
    {
        $statement = $this->db->prepare('SELECT 1 FROM categories WHERE id = :id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchColumn() !== false;
    }
}
