<section class="shop-front shop-shell">
  <nav class="shop-crumbs" aria-label="Đường dẫn">
    <a href="<?= e(base_url('/products')) ?>">Sản phẩm</a>
    <span aria-hidden="true">/</span>
    <span aria-current="page"><?= e($product['name']) ?></span>
  </nav>
  <div class="shop-product-detail">
    <div class="shop-gallery">
      <div class="shop-gallery__primary">
        <?php if ($gallery): ?>
          <img src="<?= e($gallery[0]) ?>" alt="<?= e($product['name']) ?>" width="680" height="680" fetchpriority="high">
        <?php else: ?>
          <span class="shop-picture-empty">Chưa có ảnh</span>
        <?php endif; ?>
      </div>
      <?php if (count($gallery) > 1): ?>
        <div class="shop-gallery__thumbs">
          <?php foreach ($gallery as $index => $image): ?>
            <a href="<?= e($image) ?>" aria-label="Xem ảnh <?= $index + 1 ?>">
              <img src="<?= e($image) ?>" alt="" width="72" height="72" loading="lazy">
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <div class="shop-product-info">
      <a class="shop-category shop-link" href="<?= e(base_url('/products') . '?category=' . (int) $product['category_id']) ?>"><?= e($product['category_name']) ?></a>
      <h1><?= e($product['name']) ?></h1>
      <p class="shop-prices shop-prices--large">
        <strong><?= ShopView::money(Product::unitPrice($product)) ?></strong>
        <?php if ($product['sale_price'] !== null): ?><del><?= ShopView::money((int) $product['price']) ?></del><?php endif; ?>
      </p>
      <?php if ((int) $product['stock'] > 0): ?>
        <p class="shop-product-info__stock">Tồn kho <?= number_format((int) $product['stock'], 0, ',', '.') ?> sản phẩm</p>
        <?php if (is_logged_in()): ?>
          <form class="shop-buy" action="<?= e(base_url('/cart/items/add')) ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
            <label class="shop-field" for="shop-quantity">
              <span>Số lượng</span>
              <input id="shop-quantity" type="number" name="quantity" min="1" max="<?= min((int) $product['stock'], Cart::MAX_QUANTITY) ?>" value="1" required>
            </label>
            <button class="shop-btn" type="submit">Thêm vào giỏ</button>
          </form>
        <?php else: ?>
          <a class="shop-btn" href="<?= e(base_url('/login')) ?>">Đăng nhập để mua</a>
        <?php endif; ?>
      <?php else: ?>
        <p class="shop-product-info__stock">Hiện đã hết hàng</p>
      <?php endif; ?>
      <?php if (!empty($product['description'])): ?>
        <section class="shop-product-info__description">
          <h2>Mô tả sản phẩm</h2>
          <p><?= nl2br(e($product['description'])) ?></p>
        </section>
      <?php endif; ?>
    </div>
  </div>
</section>
