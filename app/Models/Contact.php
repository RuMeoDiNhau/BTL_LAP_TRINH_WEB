<?php

declare(strict_types=1);

class Contact
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

    /** @param array{name:string,email:string,phone:?string,subject:?string,message:string,user_id:?int} $data */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO contacts (user_id, name, email, phone, subject, message) '
            . 'VALUES (:user_id, :name, :email, :phone, :subject, :message)'
        );
        $stmt->bindValue(':user_id', $data['user_id'], $data['user_id'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindValue(':phone', $data['phone'], $data['phone'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':subject', $data['subject'], $data['subject'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':message', $data['message'], PDO::PARAM_STR);
        $stmt->execute();
        return (int) $this->db->lastInsertId();
    }

    /** @return array{items:array<int,array<string,mixed>>,total:int,page:int,pages:int} */
    public function paginate(string $keyword, string $status, int $page, int $perPage = 10): array
    {
        $allowedStatuses = ['', 'unread', 'read', 'replied'];
        $status = in_array($status, $allowedStatuses, true) ? $status : '';
        $page = max(1, $page);
        $perPage = min(50, max(1, $perPage));

        $where = [];
        $params = [];
        $keyword = trim($keyword);
        if ($keyword !== '') {
            $where[] = '(name LIKE :name OR email LIKE :email OR subject LIKE :subject OR message LIKE :message)';
            $like = '%' . $keyword . '%';
            $params = [':name' => $like, ':email' => $like, ':subject' => $like, ':message' => $like];
        }
        if ($status !== '') {
            $where[] = 'status = :status';
            $params[':status'] = $status;
        }
        $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

        $count = $this->db->prepare('SELECT COUNT(*) FROM contacts' . $whereSql);
        foreach ($params as $name => $value) {
            $count->bindValue($name, $value, PDO::PARAM_STR);
        }
        $count->execute();
        $total = (int) $count->fetchColumn();
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $pages);

        $stmt = $this->db->prepare(
            'SELECT id, user_id, name, email, phone, subject, message, status, created_at FROM contacts'
            . $whereSql . ' ORDER BY created_at DESC, id DESC LIMIT :limit OFFSET :offset'
        );
        foreach ($params as $name => $value) {
            $stmt->bindValue($name, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);
        $stmt->execute();

        return ['items' => $stmt->fetchAll(), 'total' => $total, 'page' => $page, 'pages' => $pages];
    }

    public function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['unread', 'read', 'replied'], true)) {
            return false;
        }
        $stmt = $this->db->prepare('UPDATE contacts SET status = :status WHERE id = :id');
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM contacts WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }
}
