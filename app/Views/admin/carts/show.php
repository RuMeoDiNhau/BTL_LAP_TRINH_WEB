<section class="shop-admin">
  <header class="shop-admin-head">
    <div>
      <a class="shop-link" href="<?= e(base_url('/admin/carts')) ?>">← Quản lý giỏ hàng</a>
      <h1>Giỏ hàng của <?= e($cart['full_name']) ?></h1>
      <p class="shop-muted"><?= e($cart['email']) ?></p>
    </div>
    <?php if ($reviewKey !== null): ?>
      <div class="shop-row-actions">
        <span class="shop-status shop-status--<?= $reviewed ? 'completed' : 'pending' ?>"><?= $reviewed ? 'Đã xem' : 'Chưa xem' ?></span>
        <?php if (!$reviewed): ?>
          <form method="post" action="<?= e(base_url('/admin/carts/' . $userId . '/review')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="review_key" value="<?= e($reviewKey) ?>">
            <button type="submit" class="shop-btn">Đánh dấu đã xem</button>
          </form>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </header>

  <section class="shop-panel shop-order-items">
    <h2>Sản phẩm trong giỏ</h2>
    <?php if (!$items): ?>
      <p class="shop-empty">Giỏ hàng đang trống.</p>
    <?php else: ?>
      <div class="shop-table-scroll" tabindex="0" role="region" aria-label="Sản phẩm trong giỏ">
        <table class="shop-table">
          <thead><tr><th scope="col">Sản phẩm</th><th scope="col">Đơn giá</th><th scope="col">Số lượng</th><th scope="col">Thành tiền</th></tr></thead>
          <tbody>
            <?php foreach ($items as $item): ?>
              <?php $lineTotal = Cart::subtotal([$item]); ?>
              <tr>
                <th scope="row">
                  <?= e($item['name']) ?>
                  <?php if (!Cart::isPurchasable($item, (int) $item['quantity'])): ?><small class="shop-cell-secondary shop-warn">Không đủ hàng hoặc đã ngừng bán</small><?php endif; ?>
                </th>
                <td class="shop-nowrap"><?= ShopView::money(Product::unitPrice($item)) ?></td>
                <td><?= (int) $item['quantity'] ?></td>
                <td class="shop-nowrap"><?= $lineTotal === null ? '—' : ShopView::money($lineTotal) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot><tr><th scope="row" colspan="3">Tạm tính</th><td class="shop-nowrap"><?= $subtotal === null ? '—' : ShopView::money($subtotal) ?></td></tr></tfoot>
        </table>
      </div>
    <?php endif; ?>
  </section>
</section>
