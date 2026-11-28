<section class="shop-admin">
  <header class="shop-admin-head">
    <div><h1>Quản lý đơn hàng</h1><p class="shop-muted"><?= $pagination['total'] ?> đơn hàng</p></div>
  </header>
  <form class="shop-panel shop-filters" method="get" action="<?= e(base_url('/admin/orders')) ?>" role="search">
    <label class="shop-field shop-field--grow" for="admin-order-keyword">
      <span>Tìm đơn hàng</span>
      <input type="search" id="admin-order-keyword" name="keyword" value="<?= e($keyword) ?>" maxlength="100" placeholder="Mã đơn, tên, email hoặc số điện thoại">
    </label>
    <label class="shop-field" for="admin-order-status">
      <span>Trạng thái</span>
      <select id="admin-order-status" name="status">
        <option value="">Tất cả</option>
        <?php foreach (Order::STATUS_LABELS as $key => $label): ?>
          <option value="<?= e($key) ?>" <?= $status === $key ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <button class="shop-btn" type="submit">Tìm kiếm</button>
    <?php if ($keyword !== '' || $status !== ''): ?><a class="shop-btn shop-btn--outline" href="<?= e(base_url('/admin/orders')) ?>">Xóa bộ lọc</a><?php endif; ?>
  </form>
  <?php if (!$pagination['rows']): ?>
    <div class="shop-empty">Không tìm thấy đơn hàng.</div>
  <?php else: ?>
    <div class="shop-panel shop-table-scroll" tabindex="0" role="region" aria-label="Danh sách đơn hàng">
      <table class="shop-table">
        <thead><tr><th scope="col">Mã đơn</th><th scope="col">Người nhận</th><th scope="col">Ngày đặt</th><th scope="col">Trạng thái</th><th scope="col">Tổng tiền</th></tr></thead>
        <tbody>
          <?php foreach ($pagination['rows'] as $order): ?>
            <tr>
              <th scope="row"><a class="shop-link" href="<?= e(base_url('/admin/orders/' . (int) $order['id'])) ?>"><?= e($order['order_code']) ?></a></th>
              <td><?= e($order['receiver_name']) ?><small class="shop-cell-secondary"><?= e($order['account_email']) ?></small></td>
              <td class="shop-nowrap"><?= ShopView::date($order['created_at']) ?></td>
              <td><span class="shop-status shop-status--<?= e($order['status']) ?>"><?= e(Order::STATUS_LABELS[$order['status']]) ?></span></td>
              <td class="shop-nowrap"><?= ShopView::money((int) $order['total']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php
    $paginationPath = '/admin/orders';
    $paginationFilters = ['keyword' => $keyword, 'status' => $status];
    require dirname(__DIR__, 2) . '/partials/shop_pagination.php';
    ?>
  <?php endif; ?>
</section>
