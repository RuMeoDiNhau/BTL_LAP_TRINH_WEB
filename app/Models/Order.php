<?php

declare(strict_types=1);

final class Order
{
    public const STATUS_LABELS = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'shipping' => 'Đang giao',
        'completed' => 'Hoàn tất',
        'cancelled' => 'Đã hủy',
    ];

    public const PAYMENT_LABELS = ['cod' => 'Thanh toán khi nhận hàng', 'bank_transfer' => 'Chuyển khoản'];

    private const TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['shipping', 'cancelled'],
        'shipping' => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getConnection();
    }

    public function place(int $userId, array $delivery, string $checkoutKey): int
    {
        $cart = new Cart($this->db);
        $products = new Product($this->db);
        $this->db->beginTransaction();
        try {
            $cart->lockAccount($userId);
            $cartItems = $cart->quantities($userId);
            if (!$cartItems) {
                throw new DomainException('Giỏ hàng đang trống.');
            }

            $items = [];
            foreach ($cartItems as $item) {
                $product = $products->findById((int) $item['product_id'], true);
                $quantity = (int) $item['quantity'];
                if ($product === null || !Cart::isPurchasable($product, $quantity)) {
                    throw new DomainException('Có sản phẩm ngừng bán hoặc thiếu hàng. Vui lòng cập nhật giỏ.');
                }
                $items[] = $product + $item;
            }
            if (!hash_equals(Cart::checkoutKey($items), $checkoutKey)) {
                throw new DomainException('Giỏ hàng hoặc giá đã thay đổi. Vui lòng kiểm tra lại trước khi đặt.');
            }
            $total = Cart::subtotal($items);
            if ($total === null) {
                throw new DomainException('Tổng tiền vượt quá giới hạn của một đơn. Vui lòng giảm số lượng.');
            }

            $code = 'DC' . date('ymd') . strtoupper(bin2hex(random_bytes(8)));
            $statement = $this->db->prepare(
                "INSERT INTO orders (user_id, order_code, receiver_name, phone, address, note, total, payment_method)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 'cod')"
            );
            $statement->execute([
                $userId, $code, $delivery['receiver_name'], $delivery['phone'], $delivery['address'],
                $delivery['note'] === '' ? null : $delivery['note'], $total,
            ]);
            $orderId = (int) $this->db->lastInsertId();

            $addItem = $this->db->prepare(
                'INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)'
            );
            $reduceStock = $this->db->prepare('UPDATE products SET stock = stock - ? WHERE id = ?');
            foreach ($items as $item) {
                $addItem->execute([$orderId, $item['id'], $item['name'], Product::unitPrice($item), $item['quantity']]);
                $reduceStock->execute([$item['quantity'], $item['id']]);
            }
            $cart->clear($userId);
            $this->db->commit();

            return $orderId;
        } catch (Throwable $error) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $error;
        }
    }

    public function paginate(?int $userId, string $keyword, string $status, int $page, int $perPage = 12): array
    {
        $conditions = [];
        $values = [];
        if ($userId !== null) {
            $conditions[] = 'o.user_id = ?';
            $values[] = $userId;
        }
        if ($keyword !== '') {
            $conditions[] = "LOCATE(?, CONCAT_WS(' ', o.order_code, o.receiver_name, o.phone, u.email)) > 0";
            $values[] = $keyword;
        }
        if (isset(self::STATUS_LABELS[$status])) {
            $conditions[] = 'o.status = ?';
            $values[] = $status;
        }
        $from = ' FROM orders o JOIN users u ON u.id = o.user_id';
        if ($conditions) {
            $from .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $count = $this->db->prepare('SELECT COUNT(*)' . $from);
        $count->execute($values);
        $total = (int) $count->fetchColumn();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;
        $statement = $this->db->prepare(
            'SELECT o.*, u.email AS account_email' . $from
            . " ORDER BY o.created_at DESC, o.id DESC LIMIT {$perPage} OFFSET {$offset}"
        );
        $statement->execute($values);

        return ['rows' => $statement->fetchAll(), 'total' => $total, 'page' => $page, 'totalPages' => $totalPages];
    }

    public function findById(int $id, ?int $userId = null): ?array
    {
        $sql = 'SELECT o.*, u.email AS account_email FROM orders o JOIN users u ON u.id = o.user_id WHERE o.id = ?';
        $values = [$id];
        if ($userId !== null) {
            $sql .= ' AND o.user_id = ?';
            $values[] = $userId;
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($values);

        return $statement->fetch() ?: null;
    }

    public function items(int $orderId): array
    {
        $statement = $this->db->prepare('SELECT * FROM order_items WHERE order_id = ? ORDER BY product_id, id');
        $statement->execute([$orderId]);

        return $statement->fetchAll();
    }

    public static function nextStatuses(string $status): array
    {
        return self::TRANSITIONS[$status] ?? [];
    }

    public function updateStatus(int $id, string $status): bool
    {
        $this->db->beginTransaction();
        try {
            $statement = $this->db->prepare('SELECT status FROM orders WHERE id = ? FOR UPDATE');
            $statement->execute([$id]);
            $previous = $statement->fetchColumn();
            if ($previous === false) {
                throw new DomainException('Đơn hàng không còn tồn tại.');
            }
            if ($previous === $status) {
                $this->db->commit();
                return false;
            }
            if (!in_array($status, self::nextStatuses($previous), true)) {
                throw new DomainException('Không thể chuyển sang trạng thái này.');
            }

            if ($status === 'cancelled') {
                $restoreStock = $this->db->prepare('UPDATE products SET stock = stock + ? WHERE id = ? AND stock <= ?');
                foreach ($this->items($id) as $item) {
                    $quantity = (int) $item['quantity'];
                    $restoreStock->execute([$quantity, $item['product_id'], Product::MAX_UNSIGNED_INT - $quantity]);
                    if ($restoreStock->rowCount() !== 1) {
                        throw new DomainException('Không thể hoàn tồn kho. Hãy kiểm tra số lượng tồn của sản phẩm.');
                    }
                }
            }
            $statement = $this->db->prepare('UPDATE orders SET status = ? WHERE id = ?');
            $statement->execute([$status, $id]);
            $this->db->commit();

            return true;
        } catch (Throwable $error) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $error;
        }
    }
}
