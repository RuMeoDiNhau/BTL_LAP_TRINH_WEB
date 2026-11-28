<section class="shop-admin">
  <header class="shop-admin-head">
    <div>
      <a class="shop-link" href="<?= e(base_url('/admin/orders')) ?>">← Quản lý đơn hàng</a>
      <h1>Chi tiết đơn hàng</h1>
      <p class="shop-muted"><?= e($order['order_code']) ?> · <?= ShopView::date($order['created_at']) ?></p>
    </div>
    <span class="shop-status shop-status--<?= e($order['status']) ?>"><?= e(Order::STATUS_LABELS[$order['status']]) ?></span>
  </header>

  <div class="shop-columns">
    <section class="shop-panel shop-order-items">
      <h2>Sản phẩm đã đặt</h2>
      <?php require dirname(__DIR__, 2) . '/partials/shop_order_items.php'; ?>
    </section>
    <aside class="shop-panel shop-summary">
      <h2>Thông tin giao hàng</h2>
      <?php require dirname(__DIR__, 2) . '/partials/shop_order_delivery.php'; ?>
      <p class="shop-account-email"><strong>Email tài khoản</strong><br><?= e($order['account_email']) ?></p>
      <?php if ($nextStatuses): ?>
        <form class="shop-stacked shop-status-form" method="post" action="<?= e(base_url('/admin/orders/' . (int) $order['id'] . '/status')) ?>">
          <?= csrf_field() ?>
          <label class="shop-field" for="order-status">
            <span>Cập nhật trạng thái</span>
            <select id="order-status" name="status" required>
              <option value="">Chọn trạng thái</option>
              <?php foreach ($nextStatuses as $key): ?>
                <option value="<?= e($key) ?>"><?= e(Order::STATUS_LABELS[$key]) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <?php if (in_array('cancelled', $nextStatuses, true)): ?><small class="shop-muted">Hủy đơn sẽ trả số lượng đã đặt về tồn kho.</small><?php endif; ?>
          <button class="shop-btn" type="submit">Lưu trạng thái</button>
        </form>
      <?php endif; ?>
    </aside>
  </div>
</section>
