<section class="shop-front shop-shell">
  <header class="shop-page-head">
    <a class="shop-link" href="<?= e(base_url('/cart')) ?>">← Quay lại giỏ hàng</a>
    <h1>Đặt hàng</h1>
  </header>

  <div class="shop-columns">
    <form id="shop-checkout" class="shop-panel shop-form" method="post" action="<?= e(base_url('/checkout')) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="cart_key" value="<?= e($checkoutKey) ?>">
      <h2>Thông tin giao hàng</h2>
      <div class="shop-form-grid">
        <label class="shop-field" for="receiver-name">
          <span>Người nhận</span>
          <input id="receiver-name" name="receiver_name" value="<?= e($delivery['receiver_name']) ?>" autocomplete="name" required minlength="2" maxlength="100">
          <?php if (isset($errors['receiver_name'])): ?><small class="shop-warn"><?= e($errors['receiver_name']) ?></small><?php endif; ?>
        </label>
        <label class="shop-field" for="receiver-phone">
          <span>Số điện thoại</span>
          <input id="receiver-phone" name="phone" value="<?= e($delivery['phone']) ?>" type="tel" autocomplete="tel" required maxlength="20">
          <?php if (isset($errors['phone'])): ?><small class="shop-warn"><?= e($errors['phone']) ?></small><?php endif; ?>
        </label>
        <label class="shop-field shop-field--wide" for="receiver-address">
          <span>Địa chỉ giao hàng</span>
          <input id="receiver-address" name="address" value="<?= e($delivery['address']) ?>" autocomplete="street-address" required minlength="5" maxlength="255">
          <?php if (isset($errors['address'])): ?><small class="shop-warn"><?= e($errors['address']) ?></small><?php endif; ?>
        </label>
        <label class="shop-field shop-field--wide" for="order-note">
          <span>Ghi chú <small>(tùy chọn)</small></span>
          <textarea id="order-note" name="note" maxlength="255" rows="3"><?= e($delivery['note']) ?></textarea>
          <?php if (isset($errors['note'])): ?><small class="shop-warn"><?= e($errors['note']) ?></small><?php endif; ?>
        </label>
      </div>
      <p class="shop-payment">Thanh toán khi nhận hàng (COD)</p>
    </form>

    <aside class="shop-panel shop-summary">
      <h2>Đơn hàng của bạn</h2>
      <ul class="shop-summary-items">
        <?php foreach ($items as $item): ?>
          <li><span><?= e($item['name']) ?> × <?= (int) $item['quantity'] ?></span><strong><?= ShopView::money(Product::unitPrice($item) * (int) $item['quantity']) ?></strong></li>
        <?php endforeach; ?>
      </ul>
      <dl><div><dt>Tổng tiền</dt><dd><?= ShopView::money($total) ?></dd></div></dl>
      <button class="shop-btn" type="submit" form="shop-checkout">Xác nhận đặt hàng</button>
    </aside>
  </div>
</section>
