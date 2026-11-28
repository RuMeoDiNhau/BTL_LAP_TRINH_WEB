<?php

declare(strict_types=1);

final class Cart
{
    public const MAX_QUANTITY = 9999;
    private const REVIEW_KEY_PREFIX = 'cart_review:';

    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getConnection();
    }

    public function items(int $userId): array
    {
        $statement = $this->db->prepare(
            'SELECT ci.id AS cart_item_id, p.id AS product_id, p.name, p.slug, p.thumbnail, p.price, p.sale_price,
                    p.stock, p.status, ci.quantity
             FROM cart_items ci JOIN products p ON p.id = ci.product_id
             WHERE ci.user_id = ? ORDER BY ci.id'
        );
        $statement->execute([$userId]);

        return $statement->fetchAll();
    }

    public function add(int $userId, int $productId, int $quantity): void
    {
        $this->changeQuantity($userId, $productId, $quantity, true);
    }

    public function update(int $userId, int $productId, int $quantity): void
    {
        $this->changeQuantity($userId, $productId, $quantity, false);
    }

    public function remove(int $userId, int $productId): void
    {
        $this->changeQuantity($userId, $productId, 0, false);
    }

    public function paginate(string $keyword, int $page, int $perPage = 12): array
    {
        $from = ' FROM (SELECT user_id, COUNT(*) AS item_count FROM cart_items GROUP BY user_id) ci
                  JOIN users u ON u.id = ci.user_id';
        $where = '';
        $values = [];
        if ($keyword !== '') {
            $where = " WHERE LOCATE(?, CONCAT_WS(' ', u.full_name, u.email)) > 0";
            $values[] = $keyword;
        }

        $count = $this->db->prepare('SELECT COUNT(*)' . $from . $where);
        $count->execute($values);
        $total = (int) $count->fetchColumn();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;
        $statement = $this->db->prepare(
            'SELECT ci.user_id, ci.item_count, u.full_name, u.email, s.setting_value AS reviewed_key'
            . $from . ' LEFT JOIN settings s ON s.setting_key = CONCAT(?, ci.user_id)'
            . $where . " ORDER BY ci.user_id DESC LIMIT {$perPage} OFFSET {$offset}"
        );
        $statement->execute([self::REVIEW_KEY_PREFIX, ...$values]);
        $rows = $statement->fetchAll();

        $marked = array_filter($rows, static fn (array $row): bool => $row['reviewed_key'] !== null);
        $currentKeys = $this->currentKeysForUsers(array_column($marked, 'user_id'));
        foreach ($rows as $index => $row) {
            $rows[$index]['reviewed'] = $row['reviewed_key'] !== null
                && $row['reviewed_key'] === ($currentKeys[$row['user_id']] ?? null);
        }

        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'totalPages' => $totalPages];
    }

    public function findByUser(int $userId): ?array
    {
        $statement = $this->db->prepare(
            'SELECT u.full_name, u.email, s.setting_value AS reviewed_key
             FROM users u LEFT JOIN settings s ON s.setting_key = CONCAT(?, u.id)
             WHERE u.id = ?'
        );
        $statement->execute([self::REVIEW_KEY_PREFIX, $userId]);

        return $statement->fetch() ?: null;
    }

    public function markReviewed(int $userId, string $seenKey): void
    {
        $this->db->beginTransaction();
        try {
            $this->lockAccount($userId, false);
            $items = $this->items($userId);
            if (!$items) {
                throw new DomainException('Giỏ hàng đang trống.');
            }
            $currentKey = self::checkoutKey($items);
            if (!hash_equals($currentKey, $seenKey)) {
                throw new DomainException('Giỏ hàng đã thay đổi. Vui lòng xem lại trước khi đánh dấu.');
            }

            $statement = $this->db->prepare(
                'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE setting_value = ?'
            );
            $statement->execute([self::REVIEW_KEY_PREFIX . $userId, $currentKey, $currentKey]);
            $this->db->commit();
        } catch (Throwable $error) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $error;
        }
    }

    /** Tuần tự hóa thao tác cùng tài khoản trong giao dịch, kể cả khi giỏ đang trống. */
    public function lockAccount(int $userId, bool $requireActive = true): void
    {
        $sql = 'SELECT id FROM users WHERE id = ?';
        if ($requireActive) {
            $sql .= " AND status = 'active'";
        }
        $statement = $this->db->prepare($sql . ' FOR UPDATE');
        $statement->execute([$userId]);
        if ($statement->fetchColumn() === false) {
            throw new DomainException($requireActive
                ? 'Tài khoản không còn khả dụng. Vui lòng đăng nhập lại.'
                : 'Không tìm thấy khách hàng này.');
        }
    }

    public function quantities(int $userId): array
    {
        $statement = $this->db->prepare(
            'SELECT id AS cart_item_id, product_id, quantity FROM cart_items WHERE user_id = ? ORDER BY product_id'
        );
        $statement->execute([$userId]);

        return $statement->fetchAll();
    }

    public function clear(int $userId): void
    {
        $statement = $this->db->prepare('DELETE FROM cart_items WHERE user_id = ?');
        $statement->execute([$userId]);
        $this->clearReview($userId);
    }

    public static function isPurchasable(array $product, int $quantity): bool
    {
        return $product['status'] === 'active' && $quantity >= 1
            && $quantity <= self::MAX_QUANTITY && $quantity <= (int) $product['stock'];
    }

    public static function subtotal(array $items): ?int
    {
        $total = 0;
        foreach ($items as $item) {
            $quantity = (int) $item['quantity'];
            $price = Product::unitPrice($item);
            if ($quantity < 1 || $price > intdiv(Product::MAX_UNSIGNED_INT - $total, $quantity)) {
                return null;
            }
            $total += $price * $quantity;
        }

        return $total;
    }

    public static function checkoutProblem(array $items, ?int $subtotal): ?string
    {
        if (!$items) {
            return 'Giỏ hàng đang trống.';
        }
        foreach ($items as $item) {
            if (!self::isPurchasable($item, (int) $item['quantity'])) {
                return 'Cập nhật hoặc xóa sản phẩm không còn đủ hàng để tiếp tục đặt hàng.';
            }
        }
        if ($subtotal === null) {
            return 'Tổng tiền vượt quá giới hạn của một đơn. Vui lòng giảm số lượng.';
        }

        return null;
    }

    public static function checkoutKey(array $items): string
    {
        $lines = [];
        foreach ($items as $item) {
            $lines[(int) $item['product_id']] = [
                (int) $item['cart_item_id'],
                (int) $item['quantity'],
                Product::unitPrice($item),
            ];
        }
        ksort($lines);

        return hash('sha256', serialize($lines));
    }

    private function currentKeysForUsers(array $userIds): array
    {
        if (!$userIds) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($userIds), '?'));
        $statement = $this->db->prepare(
            "SELECT ci.user_id, ci.id AS cart_item_id, ci.product_id, ci.quantity, p.price, p.sale_price
             FROM cart_items ci JOIN products p ON p.id = ci.product_id
             WHERE ci.user_id IN ({$placeholders})"
        );
        $statement->execute($userIds);
        $itemsByUser = [];
        foreach ($statement->fetchAll() as $item) {
            $itemsByUser[(int) $item['user_id']][] = $item;
        }

        return array_map([self::class, 'checkoutKey'], $itemsByUser);
    }

    private function clearReview(int $userId): void
    {
        $statement = $this->db->prepare('DELETE FROM settings WHERE setting_key = ?');
        $statement->execute([self::REVIEW_KEY_PREFIX . $userId]);
    }

    private function changeQuantity(int $userId, int $productId, int $quantity, bool $adding): void
    {
        $this->db->beginTransaction();
        try {
            $this->lockAccount($userId);
            if ($quantity === 0) {
                $statement = $this->db->prepare('DELETE FROM cart_items WHERE user_id = ? AND product_id = ?');
                $statement->execute([$userId, $productId]);
                if ($statement->rowCount() !== 1) {
                    throw new DomainException('Sản phẩm không có trong giỏ.');
                }
            } else {
                $product = (new Product($this->db))->findById($productId, true);
                if ($product === null || $product['status'] !== 'active') {
                    throw new DomainException('Sản phẩm hiện không bán. Bạn có thể xóa sản phẩm khỏi giỏ.');
                }

                $statement = $this->db->prepare('SELECT quantity FROM cart_items WHERE user_id = ? AND product_id = ?');
                $statement->execute([$userId, $productId]);
                $previous = $statement->fetchColumn();
                if (!$adding && $previous === false) {
                    throw new DomainException('Sản phẩm không có trong giỏ.');
                }
                $newQuantity = $adding ? $quantity + (int) $previous : $quantity;
                if (!self::isPurchasable($product, $newQuantity)) {
                    throw new DomainException('Số lượng yêu cầu vượt quá số lượng có thể mua.');
                }
                if ($newQuantity === (int) $previous) {
                    $this->db->commit();
                    return;
                }

                $statement = $this->db->prepare(
                    'INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)
                     ON DUPLICATE KEY UPDATE quantity = ?'
                );
                $statement->execute([$userId, $productId, $newQuantity, $newQuantity]);
            }
            $this->clearReview($userId);
            $this->db->commit();
        } catch (Throwable $error) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $error;
        }
    }
}
