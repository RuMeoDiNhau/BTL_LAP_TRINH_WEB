<section class="shop-front shop-shell">
  <header class="shop-page-head">
    <a class="shop-link" href="<?= e(base_url('/orders')) ?>">← Đơn hàng của tôi</a>
    <h1>Chi tiết đơn hàng</h1>
    <div class="shop-order-meta">
      <strong><?= e($order['order_code']) ?></strong>
      <span class="shop-muted"><?= ShopView::date($order['created_at']) ?></span>
      <span class="shop-status shop-status--<?= e($order['status']) ?>"><?= e(Order::STATUS_LABELS[$order['status']]) ?></span>
    </div>
  </header>

  <div class="shop-columns">
    <section class="shop-panel shop-order-items">
      <h2>Sản phẩm đã đặt</h2>
      <?php require dirname(__DIR__, 2) . '/partials/shop_order_items.php'; ?>
    </section>
    <aside class="shop-panel shop-summary">
      <h2>Thông tin giao hàng</h2>
      <?php require dirname(__DIR__, 2) . '/partials/shop_order_delivery.php'; ?>
    </aside>
  </div>
</section>
