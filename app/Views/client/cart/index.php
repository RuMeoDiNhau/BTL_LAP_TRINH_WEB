<section class="shop-front shop-shell">
  <header class="shop-page-head">
    <a class="shop-link" href="<?= e(base_url('/products')) ?>">← Tiếp tục mua sắm</a>
    <h1>Giỏ hàng</h1>
  </header>

  <?php if (!$items): ?>
    <div class="shop-empty">
      <h2>Giỏ hàng đang trống</h2>
      <a class="shop-btn" href="<?= e(base_url('/products')) ?>">Khám phá sản phẩm</a>
    </div>
  <?php else: ?>
    <div class="shop-columns">
      <section class="shop-panel shop-cart-list" aria-label="Sản phẩm trong giỏ">
        <?php foreach ($items as $item): ?>
          <?php
          $image = ShopView::image($item['thumbnail']);
          $productId = (int) $item['product_id'];
          $quantity = (int) $item['quantity'];
          $available = Cart::isPurchasable($item, $quantity);
          $lineTotal = Cart::subtotal([$item]);
          ?>
          <article class="shop-cart-row">
            <div class="shop-cart-row__image">
              <?php if ($image !== null): ?><img src="<?= e($image) ?>" alt="" loading="lazy" width="84" height="84"><?php endif; ?>
            </div>
            <div class="shop-cart-row__info">
              <h2>
                <?php if ($item['status'] === 'active'): ?>
                  <a href="<?= e(base_url('/products/' . rawurlencode($item['slug']))) ?>"><?= e($item['name']) ?></a>
                <?php else: ?>
                  <?= e($item['name']) ?>
                <?php endif; ?>
              </h2>
              <p class="shop-muted"><?= ShopView::money(Product::unitPrice($item)) ?> / sản phẩm</p>
              <?php if (!$available): ?>
                <p class="shop-warn"><?= $item['status'] !== 'active' ? 'Ngừng bán' : 'Số lượng trong giỏ vượt mức có thể mua' ?></p>
              <?php endif; ?>
              <div class="shop-row-actions">
                <?php if ($item['status'] === 'active' && (int) $item['stock'] > 0): ?>
                  <?php $limit = min((int) $item['stock'], Cart::MAX_QUANTITY); ?>
                  <form class="shop-quantity" method="post" action="<?= e(base_url('/cart/items/' . $productId . '/update')) ?>">
                    <?= csrf_field() ?>
                    <label class="shop-sr-only" for="qty-<?= $productId ?>">Số lượng <?= e($item['name']) ?></label>
                    <input id="qty-<?= $productId ?>" type="number" name="quantity" min="1" max="<?= $limit ?>" value="<?= min($quantity, $limit) ?>" required>
                    <button type="submit" class="shop-btn shop-btn--small shop-btn--outline">Cập nhật</button>
                  </form>
                <?php else: ?>
                  <span class="shop-muted">Số lượng <?= $quantity ?></span>
                <?php endif; ?>
                <form method="post" action="<?= e(base_url('/cart/items/' . $productId . '/remove')) ?>">
                  <?= csrf_field() ?>
                  <button class="shop-text-btn" type="submit">Xóa<span class="shop-sr-only"> <?= e($item['name']) ?></span></button>
                </form>
              </div>
            </div>
            <strong class="shop-cart-row__total"><?= $lineTotal === null ? '—' : ShopView::money($lineTotal) ?></strong>
          </article>
        <?php endforeach; ?>
      </section>

      <aside class="shop-panel shop-summary">
        <h2>Tóm tắt giỏ hàng</h2>
        <dl><div><dt>Tạm tính</dt><dd><?= $subtotal === null ? '—' : ShopView::money($subtotal) ?></dd></div></dl>
        <?php if ($checkoutProblem === null): ?>
          <a class="shop-btn" href="<?= e(base_url('/checkout')) ?>">Tiến hành đặt hàng</a>
        <?php else: ?>
          <p class="shop-warn"><?= e($checkoutProblem) ?></p>
        <?php endif; ?>
        <a class="shop-link" href="<?= e(base_url('/orders')) ?>">Xem đơn hàng của tôi</a>
      </aside>
    </div>
  <?php endif; ?>
</section>
