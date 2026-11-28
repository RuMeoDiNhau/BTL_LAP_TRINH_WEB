<section class="shop-front shop-shell">
  <header class="shop-hero">
    <div class="shop-hero__copy">
      <h1>Nội thất &amp;<br> đồ trang trí</h1>
    </div>
    <img class="shop-hero__image" src="<?= e(base_url('/decor-template/decor/images/banner-large-1.jpg')) ?>" alt="" fetchpriority="high">
  </header>

  <form class="shop-filters shop-catalog-filters" method="get" action="<?= e(base_url('/products')) ?>" role="search">
    <label class="shop-field shop-field--grow" for="shop-keyword">
      <span>Tìm sản phẩm</span>
      <input type="search" id="shop-keyword" name="keyword" value="<?= e($keyword) ?>" placeholder="Ví dụ: đèn, ghế, bình gốm" maxlength="100">
    </label>
    <label class="shop-field" for="shop-category">
      <span>Danh mục</span>
      <select id="shop-category" name="category">
        <option value="">Tất cả danh mục</option>
        <?php foreach ($categories as $category): ?>
          <option value="<?= (int) $category['id'] ?>" <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <button class="shop-btn" type="submit">Tìm kiếm</button>
    <?php if ($keyword !== '' || $categoryId > 0): ?>
      <a class="shop-btn shop-btn--outline" href="<?= e(base_url('/products')) ?>">Xóa bộ lọc</a>
    <?php endif; ?>
  </form>

  <div class="shop-section-head">
    <h2><?= $keyword === '' ? 'Sản phẩm' : 'Kết quả tìm kiếm' ?></h2>
    <span class="shop-muted"><?= $pagination['total'] ?> sản phẩm</span>
  </div>
  <?php if (!$pagination['rows']): ?>
    <div class="shop-empty">
      <p>Không tìm thấy sản phẩm phù hợp.</p>
      <?php if ($keyword !== '' || $categoryId > 0): ?><a class="shop-link" href="<?= e(base_url('/products')) ?>">Xem tất cả sản phẩm</a><?php endif; ?>
    </div>
  <?php else: ?>
    <div class="shop-product-grid">
      <?php foreach ($pagination['rows'] as $product): ?>
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
      <?php endforeach; ?>
    </div>
    <?php
    $paginationPath = '/products';
    $paginationFilters = array_filter(['keyword' => $keyword, 'category' => $categoryId], static fn ($value) => $value !== '' && $value !== 0);
    require dirname(__DIR__, 2) . '/partials/shop_pagination.php';
    ?>
  <?php endif; ?>
</section>
