<?php

declare(strict_types=1);

final class Product
{
    public const STATUSES = ['active', 'hidden'];
    public const MAX_UNSIGNED_INT = 4294967295;

    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getConnection();
    }

    public function paginate(string $keyword, int $category, string $status, int $page, int $perPage): array
    {
        $conditions = [];
        $parameters = [];
        if ($keyword !== '') {
            $conditions[] = "(p.name LIKE :name ESCAPE '!' OR p.description LIKE :description ESCAPE '!')";
            $pattern = '%' . strtr($keyword, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
            $parameters[':name'] = $pattern;
            $parameters[':description'] = $pattern;
        }
        if ($category > 0) {
            $conditions[] = 'p.category_id = :category';
            $parameters[':category'] = $category;
        }
        if (in_array($status, self::STATUSES, true)) {
            $conditions[] = 'p.status = :status';
            $parameters[':status'] = $status;
        }

        $from = ' FROM products p JOIN categories c ON c.id = p.category_id';
        if ($conditions) {
            $from .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $count = $this->query('SELECT COUNT(*)' . $from, $parameters);
        $total = (int) $count->fetchColumn();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));

        $rows = $this->query(
            'SELECT p.*, c.name AS category_name' . $from
            . ' ORDER BY p.created_at DESC, p.id DESC LIMIT :limit OFFSET :offset',
            $parameters + [':limit' => $perPage, ':offset' => ($page - 1) * $perPage]
        )->fetchAll();

        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'totalPages' => $totalPages];
    }

    public function findActiveBySlug(string $slug): ?array
    {
        $statement = $this->query(
            "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id
             WHERE p.slug = :slug AND p.status = 'active'",
            [':slug' => $slug]
        );

        return $statement->fetch() ?: null;
    }

    public function findById(int $id, bool $forUpdate = false): ?array
    {
        $sql = 'SELECT * FROM products WHERE id = :id' . ($forUpdate ? ' FOR UPDATE' : '');
        return $this->query($sql, [':id' => $id])->fetch() ?: null;
    }

    public function images(int $id): array
    {
        return $this->query(
            'SELECT image_path FROM product_images WHERE product_id = :id ORDER BY sort_order, id',
            [':id' => $id]
        )->fetchAll(PDO::FETCH_COLUMN);
    }

    public function create(array $data): int
    {
        $slug = self::slug($data['name']);
        $parameters = $this->productParameters($data) + [':slug' => $slug, ':thumbnail' => $data['thumbnail']];
        $sql = 'INSERT INTO products (category_id, name, slug, description, price, sale_price, stock, thumbnail, status)
                VALUES (:category, :name, :slug, :description, :price, :sale, :stock, :thumbnail, :status)';

        try {
            $this->query($sql, $parameters);
        } catch (PDOException $error) {
            if ((int) ($error->errorInfo[1] ?? 0) !== 1062) {
                throw $error;
            }
            $parameters[':slug'] = $slug . '-' . bin2hex(random_bytes(5));
            $this->query($sql, $parameters);
        }

        return (int) $this->db->lastInsertId();
    }

    /** Giữ slug cũ và trả đường dẫn ảnh trước khi thay. */
    public function update(int $id, array $data, string $revision): ?string
    {
        $this->db->beginTransaction();
        try {
            $product = $this->findById($id, true);
            if ($product === null) {
                throw new DomainException('Sản phẩm không còn tồn tại.');
            }
            if (!hash_equals(self::revision($product), $revision)) {
                throw new DomainException('Sản phẩm đã thay đổi. Mở lại biểu mẫu để xem dữ liệu mới rồi lưu.');
            }

            $thumbnail = $data['thumbnail'] ?? $product['thumbnail'];
            $this->query(
                'UPDATE products SET category_id = :category, name = :name, description = :description,
                 price = :price, sale_price = :sale, stock = :stock, thumbnail = :thumbnail, status = :status
                 WHERE id = :id',
                $this->productParameters($data) + [':id' => $id, ':thumbnail' => $thumbnail]
            );
            $this->db->commit();

            return $data['thumbnail'] === null ? null : $product['thumbnail'];
        } catch (Throwable $error) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $error;
        }
    }

    /** Đơn hàng đã lưu tiếp tục tham chiếu sản phẩm được ẩn. */
    public function deleteOrHide(int $id): array
    {
        $this->db->beginTransaction();
        try {
            $product = $this->findById($id, true);
            if ($product === null) {
                throw new DomainException('Sản phẩm không còn tồn tại.');
            }

            try {
                $this->query('DELETE FROM products WHERE id = :id', [':id' => $id]);
                $result = ['hidden' => false, 'thumbnail' => $product['thumbnail']];
            } catch (PDOException $error) {
                if ((int) ($error->errorInfo[1] ?? 0) !== 1451) {
                    throw $error;
                }
                $this->query("UPDATE products SET status = 'hidden' WHERE id = :id", [':id' => $id]);
                $result = ['hidden' => true, 'thumbnail' => null];
            }

            $this->db->commit();
            return $result;
        } catch (Throwable $error) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $error;
        }
    }

    public static function unitPrice(array $product): int
    {
        return (int) ($product['sale_price'] ?? $product['price']);
    }

    public static function revision(array $product): string
    {
        return hash('sha256', serialize($product));
    }

    private function productParameters(array $data): array
    {
        return [
            ':category' => $data['category_id'],
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':sale' => $data['sale_price'],
            ':stock' => $data['stock'],
            ':status' => $data['status'],
        ];
    }

    private function query(string $sql, array $parameters): PDOStatement
    {
        $statement = $this->db->prepare($sql);
        foreach ($parameters as $key => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : ($value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $statement->bindValue($key, $value, $type);
        }
        $statement->execute();

        return $statement;
    }

    private static function slug(string $name): string
    {
        $slug = Post::generateSlug($name);
        if ($slug === '') {
            return 'san-pham';
        }

        $slug = ctype_digit($slug) ? 'san-pham-' . $slug : $slug;

        return rtrim(substr($slug, 0, 200), '-');
    }
}
