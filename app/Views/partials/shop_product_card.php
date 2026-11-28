<?php $image = ShopView::image($product['thumbnail']); ?>
<article class="shop-product-card">
  <a href="<?= e(base_url('/products/' . rawurlencode($product['slug']))) ?>">
    <div class="shop-product-card__picture">
      <?php if ($image !== null): ?>
        <img src="<?= e($image) ?>" alt="" loading="lazy" width="400" height="400">
      <?php else: ?>
        <span class="shop-picture-empty">Chưa có ảnh</span>
      <?php endif; ?>
      <?php if ((int) $product['stock'] === 0): ?>
        <span class="shop-product-card__stock">Hết hàng</span>
      <?php endif; ?>
    </div>
    <div class="shop-product-card__body">
      <p class="shop-category"><?= e($product['category_name']) ?></p>
      <h3><?= e($product['name']) ?></h3>
      <p class="shop-prices">
        <strong><?= ShopView::money(Product::unitPrice($product)) ?></strong>
        <?php if ($product['sale_price'] !== null): ?><del><?= ShopView::money((int) $product['price']) ?></del><?php endif; ?>
      </p>
    </div>
  </a>
</article>
