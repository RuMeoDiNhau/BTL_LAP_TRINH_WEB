<section class="shop-admin">
  <header class="shop-admin-head">
    <div><h1>Quản lý giỏ hàng</h1><p class="shop-muted"><?= $pagination['total'] ?> giỏ hàng</p></div>
  </header>
  <form class="shop-panel shop-filters" method="get" action="<?= e(base_url('/admin/carts')) ?>" role="search">
    <label class="shop-field shop-field--grow" for="admin-cart-keyword">
      <span>Tìm khách hàng</span>
      <input type="search" id="admin-cart-keyword" name="keyword" value="<?= e($keyword) ?>" maxlength="100" placeholder="Họ tên hoặc email">
    </label>
    <button class="shop-btn" type="submit">Tìm kiếm</button>
    <?php if ($keyword !== ''): ?><a class="shop-btn shop-btn--outline" href="<?= e(base_url('/admin/carts')) ?>">Xóa bộ lọc</a><?php endif; ?>
  </form>
  <?php if (!$pagination['rows']): ?>
    <div class="shop-empty">Không tìm thấy giỏ hàng.</div>
  <?php else: ?>
    <div class="shop-panel shop-table-scroll" tabindex="0" role="region" aria-label="Danh sách giỏ hàng">
      <table class="shop-table">
        <thead><tr><th scope="col">Khách hàng</th><th scope="col">Mặt hàng</th><th scope="col">Trạng thái</th><th scope="col">Chi tiết</th></tr></thead>
        <tbody>
          <?php foreach ($pagination['rows'] as $cart): ?>
            <tr>
              <th scope="row"><?= e($cart['full_name']) ?><small class="shop-cell-secondary"><?= e($cart['email']) ?></small></th>
              <td><?= (int) $cart['item_count'] ?></td>
              <td><span class="shop-status shop-status--<?= $cart['reviewed'] ? 'completed' : 'pending' ?>"><?= $cart['reviewed'] ? 'Đã xem' : 'Chưa xem' ?></span></td>
              <td><a class="shop-link" href="<?= e(base_url('/admin/carts/' . (int) $cart['user_id'])) ?>">Xem giỏ<span class="shop-sr-only"> của <?= e($cart['full_name']) ?></span></a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php
    $paginationPath = '/admin/carts';
    $paginationFilters = ['keyword' => $keyword];
    require dirname(__DIR__, 2) . '/partials/shop_pagination.php';
    ?>
  <?php endif; ?>
</section>
