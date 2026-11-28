<?php
$formPath = $product === null ? '/admin/products/store' : '/admin/products/' . (int) $product['id'] . '/update';
$image = ShopView::image($values['thumbnail']);
?>
<section class="shop-admin">
  <header class="shop-admin-head">
    <div><a class="shop-link" href="<?= e(base_url('/admin/products')) ?>">← Quản lý sản phẩm</a><h1><?= e($title) ?></h1></div>
  </header>
  <form class="shop-panel shop-form shop-product-form" method="post" action="<?= e(base_url($formPath)) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if ($product !== null): ?><input type="hidden" name="revision" value="<?= e($values['revision']) ?>"><?php endif; ?>
    <div class="shop-form-grid">
      <label class="shop-field shop-field--wide" for="product-name">
        <span>Tên sản phẩm</span>
        <input id="product-name" name="name" value="<?= e((string) $values['name']) ?>" required minlength="2" maxlength="200">
        <?php if (isset($errors['name'])): ?><small class="shop-warn"><?= e($errors['name']) ?></small><?php endif; ?>
      </label>
      <label class="shop-field" for="product-category">
        <span>Danh mục</span>
        <select id="product-category" name="category_id" required>
          <option value="">Chọn danh mục</option>
          <?php foreach ($categories as $category): ?>
            <option value="<?= (int) $category['id'] ?>" <?= (int) $values['category_id'] === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($errors['category_id'])): ?><small class="shop-warn"><?= e($errors['category_id']) ?></small><?php endif; ?>
      </label>
      <label class="shop-field" for="product-status">
        <span>Trạng thái hiển thị</span>
        <select id="product-status" name="status" required>
          <option value="active" <?= $values['status'] === 'active' ? 'selected' : '' ?>>Đang bán</option>
          <option value="hidden" <?= $values['status'] === 'hidden' ? 'selected' : '' ?>>Ẩn</option>
        </select>
        <?php if (isset($errors['status'])): ?><small class="shop-warn"><?= e($errors['status']) ?></small><?php endif; ?>
      </label>
      <label class="shop-field" for="product-price">
        <span>Giá gốc (₫)</span>
        <input id="product-price" name="price" value="<?= e((string) $values['price']) ?>" type="number" min="0" max="<?= Product::MAX_UNSIGNED_INT ?>" step="1" required>
        <?php if (isset($errors['price'])): ?><small class="shop-warn"><?= e($errors['price']) ?></small><?php endif; ?>
      </label>
      <label class="shop-field" for="product-sale-price">
        <span>Giá ưu đãi (₫) <small>(tùy chọn)</small></span>
        <input id="product-sale-price" name="sale_price" value="<?= e((string) $values['sale_price']) ?>" type="number" min="0" max="<?= Product::MAX_UNSIGNED_INT ?>" step="1">
        <?php if (isset($errors['sale_price'])): ?><small class="shop-warn"><?= e($errors['sale_price']) ?></small><?php endif; ?>
      </label>
      <label class="shop-field" for="product-stock">
        <span>Tồn kho</span>
        <input id="product-stock" name="stock" value="<?= e((string) $values['stock']) ?>" type="number" min="0" max="<?= Product::MAX_UNSIGNED_INT ?>" step="1" required>
        <?php if (isset($errors['stock'])): ?><small class="shop-warn"><?= e($errors['stock']) ?></small><?php endif; ?>
      </label>
      <label class="shop-field shop-field--wide" for="product-description">
        <span>Mô tả</span>
        <textarea id="product-description" name="description" maxlength="10000" rows="6"><?= e((string) $values['description']) ?></textarea>
        <?php if (isset($errors['description'])): ?><small class="shop-warn"><?= e($errors['description']) ?></small><?php endif; ?>
      </label>
      <div class="shop-field--wide shop-upload">
        <?php if ($image !== null): ?>
          <img src="<?= e($image) ?>" alt="Ảnh hiện tại của sản phẩm" width="100" height="100">
        <?php endif; ?>
        <label class="shop-field" for="product-thumbnail">
          <span><?= $image === null ? 'Ảnh đại diện sản phẩm' : 'Thay ảnh đại diện' ?></span>
          <input id="product-thumbnail" name="thumbnail" type="file" accept="image/jpeg,image/png,image/webp" aria-describedby="product-thumbnail-help">
          <small id="product-thumbnail-help">JPG, PNG hoặc WebP, tối đa 2 MB và 4096 × 4096 pixel.</small>
          <?php if (isset($errors['thumbnail'])): ?><small class="shop-warn"><?= e($errors['thumbnail']) ?></small><?php endif; ?>
        </label>
      </div>
    </div>
    <div class="shop-row-actions">
      <button class="shop-btn" type="submit">Lưu sản phẩm</button>
      <a class="shop-btn shop-btn--outline" href="<?= e(base_url('/admin/products')) ?>">Hủy</a>
    </div>
  </form>
</section>
