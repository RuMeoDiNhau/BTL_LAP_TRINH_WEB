<section class="shop-front shop-shell">
  <header class="shop-page-head">
    <a class="shop-link" href="<?= e(base_url('/products')) ?>">← Tiếp tục mua sắm</a>
    <h1>Đơn hàng của tôi</h1>
  </header>
  <?php if (!$pagination['rows']): ?>
    <div class="shop-empty"><p>Bạn chưa có đơn hàng nào.</p><a class="shop-btn" href="<?= e(base_url('/products')) ?>">Xem sản phẩm</a></div>
  <?php else: ?>
    <div class="shop-panel shop-table-scroll" tabindex="0" role="region" aria-label="Danh sách đơn hàng">
      <table class="shop-table">
        <thead><tr><th scope="col">Mã đơn</th><th scope="col">Ngày đặt</th><th scope="col">Trạng thái</th><th scope="col">Tổng tiền</th></tr></thead>
        <tbody>
          <?php foreach ($pagination['rows'] as $order): ?>
            <tr>
              <th scope="row"><a class="shop-link" href="<?= e(base_url('/orders/' . (int) $order['id'])) ?>"><?= e($order['order_code']) ?></a></th>
              <td class="shop-nowrap"><?= ShopView::date($order['created_at']) ?></td>
              <td><span class="shop-status shop-status--<?= e($order['status']) ?>"><?= e(Order::STATUS_LABELS[$order['status']]) ?></span></td>
              <td class="shop-nowrap"><?= ShopView::money((int) $order['total']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php
    $paginationPath = '/orders';
    require dirname(__DIR__, 2) . '/partials/shop_pagination.php';
    ?>
  <?php endif; ?>
</section>
