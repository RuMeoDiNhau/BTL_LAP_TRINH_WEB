<section class="shop-admin">
  <header class="shop-admin-head">
    <div><h1>Quản lý sản phẩm</h1><p class="shop-muted"><?= $pagination['total'] ?> sản phẩm</p></div>
    <a class="shop-btn" href="<?= e(base_url('/admin/products/create')) ?>">Thêm sản phẩm</a>
  </header>
  <form class="shop-panel shop-filters" method="get" action="<?= e(base_url('/admin/products')) ?>" role="search">
    <label class="shop-field shop-field--grow" for="admin-product-keyword">
      <span>Tìm sản phẩm</span>
      <input type="search" id="admin-product-keyword" name="keyword" value="<?= e($keyword) ?>" maxlength="100" placeholder="Tên hoặc mô tả">
    </label>
    <label class="shop-field" for="admin-product-category">
      <span>Danh mục</span>
      <select id="admin-product-category" name="category">
        <option value="">Tất cả</option>
        <?php foreach ($categories as $category): ?>
          <option value="<?= (int) $category['id'] ?>" <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label class="shop-field" for="admin-product-status">
      <span>Hiển thị</span>
      <select id="admin-product-status" name="status">
        <option value="">Tất cả</option>
        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Đang bán</option>
        <option value="hidden" <?= $status === 'hidden' ? 'selected' : '' ?>>Đã ẩn</option>
      </select>
    </label>
    <button class="shop-btn" type="submit">Tìm kiếm</button>
    <?php if ($keyword !== '' || $categoryId > 0 || $status !== ''): ?>
      <a class="shop-btn shop-btn--outline" href="<?= e(base_url('/admin/products')) ?>">Xóa bộ lọc</a>
    <?php endif; ?>
  </form>
  <?php if (!$pagination['rows']): ?>
    <div class="shop-empty">Không tìm thấy sản phẩm.</div>
  <?php else: ?>
    <div class="shop-panel shop-table-scroll" tabindex="0" role="region" aria-label="Danh sách sản phẩm">
      <table class="shop-table">
        <thead><tr><th scope="col">Sản phẩm</th><th scope="col">Giá bán</th><th scope="col">Tồn kho</th><th scope="col">Hiển thị</th><th scope="col">Thao tác</th></tr></thead>
        <tbody>
          <?php foreach ($pagination['rows'] as $product): ?>
            <?php $image = ShopView::image($product['thumbnail']); $productId = (int) $product['id']; ?>
            <tr>
              <th scope="row">
                <div class="shop-cell-product">
                  <div class="shop-cell-picture"><?php if ($image !== null): ?><img src="<?= e($image) ?>" alt="" loading="lazy" width="48" height="48"><?php endif; ?></div>
                  <div><?= e($product['name']) ?><small class="shop-muted"><?= e($product['category_name']) ?></small></div>
                </div>
              </th>
              <td class="shop-nowrap"><?= ShopView::money(Product::unitPrice($product)) ?></td>
              <td><?= (int) $product['stock'] ?></td>
              <td><span class="shop-status shop-status--<?= e($product['status']) ?>"><?= $product['status'] === 'active' ? 'Đang bán' : 'Đã ẩn' ?></span></td>
              <td>
                <div class="shop-row-actions">
                  <a class="shop-link" href="<?= e(base_url('/admin/products/' . $productId . '/edit')) ?>">Sửa<span class="shop-sr-only"> <?= e($product['name']) ?></span></a>
                  <?php if ($product['status'] === 'active'): ?>
                    <a class="shop-link" href="<?= e(base_url('/products/' . rawurlencode($product['slug']))) ?>">Xem<span class="shop-sr-only"> <?= e($product['name']) ?></span></a>
                  <?php endif; ?>
                  <form method="post" action="<?= e(base_url('/admin/products/' . $productId . '/delete')) ?>"
                        data-shop-confirm="Xóa sản phẩm này? Sản phẩm đã có trong đơn hàng sẽ được ẩn.">
                    <?= csrf_field() ?>
                    <button class="shop-text-btn" type="submit">Xóa<span class="shop-sr-only"> <?= e($product['name']) ?></span></button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php
    $paginationPath = '/admin/products';
    $paginationFilters = array_filter(
        ['keyword' => $keyword, 'category' => $categoryId, 'status' => $status],
        static fn ($value) => $value !== '' && $value !== 0
    );
    require dirname(__DIR__, 2) . '/partials/shop_pagination.php';
    ?>
  <?php endif; ?>
</section>
