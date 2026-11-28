<dl class="shop-details">
  <div><dt>Người nhận</dt><dd><?= e($order['receiver_name']) ?></dd></div>
  <div><dt>Điện thoại</dt><dd><?= e($order['phone']) ?></dd></div>
  <div><dt>Địa chỉ</dt><dd><?= e($order['address']) ?></dd></div>
  <div><dt>Thanh toán</dt><dd><?= e(Order::PAYMENT_LABELS[$order['payment_method']]) ?></dd></div>
  <?php if ($order['note'] !== null && $order['note'] !== ''): ?>
    <div><dt>Ghi chú</dt><dd><?= nl2br(e($order['note'])) ?></dd></div>
  <?php endif; ?>
</dl>
