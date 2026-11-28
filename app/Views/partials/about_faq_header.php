<?php
$dcHeaderNav = [
    ['Trang chủ', '/', 'home'],
    ['Sản phẩm', '/products', 'products'],
    ['Giới thiệu', '/about', 'about'],
    ['Tin tức', '/posts', 'posts'],
    ['FAQ', '/faq', 'faq'],
    ['Liên hệ', '/contact', 'contact'],
];
$dcShopSearch = in_array($activeMenu, ['home', 'products', 'cart', 'orders'], true);
$dcUser = current_user();
$dcUserName = $dcUser['full_name'] ?? '';
$dcAvatar = avatar_url($dcUser['avatar'] ?? null);
?>
<header class="dc-shell-header">
  <a class="dc-shell-logo" href="<?= e(base_url('/')) ?>" aria-label="Decor – Trang chủ">
    <?php if (!empty($brandLogo)): ?>
      <img src="<?= e($brandLogo) ?>" alt="" width="120" height="40">
    <?php else: ?>
      Decor
    <?php endif; ?>
  </a>

  <!-- hidden trên mobile; CSS min-width:992px luôn hiện nav để desktop không phụ thuộc JS. -->
  <nav id="dcShellMenu" class="dc-shell-nav" aria-label="Menu chính" hidden>
    <ul>
      <?php foreach ($dcHeaderNav as [$dcLabel, $dcPath, $dcKey]): ?>
        <li>
          <a href="<?= e(base_url($dcPath)) ?>"<?= $activeMenu === $dcKey ? ' class="is-active" aria-current="page"' : '' ?>><?= e($dcLabel) ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>

  <div class="dc-shell-actions">
    <details class="dc-shell-menu">
      <summary class="dc-shell-icon" aria-label="Mở tìm kiếm"><?= $dcIcon('search', 18) ?></summary>
      <div class="dc-shell-dropdown dc-shell-search">
        <form role="search" method="get" action="<?= e(url($dcShopSearch ? 'products' : 'faq')) ?>">
          <label class="dc-shell-visually-hidden" for="dcShellSearch">Tìm kiếm</label>
          <input id="dcShellSearch" type="search" name="<?= $dcShopSearch ? 'keyword' : 's' ?>" placeholder="<?= $dcShopSearch ? 'Tìm sản phẩm' : 'Nhập và nhấn Enter' ?>" autocomplete="off">
          <button type="submit" aria-label="Gửi tìm kiếm"><?= $dcIcon('search', 16) ?></button>
        </form>
      </div>
    </details>

    <a class="dc-shell-icon" href="<?= e(base_url('/cart')) ?>" aria-label="Giỏ hàng" title="Giỏ hàng"<?= $activeMenu === 'cart' ? ' aria-current="page"' : '' ?>><?= $dcIcon('bag', 18) ?></a>

    <details class="dc-shell-menu">
      <summary class="dc-shell-icon dc-shell-account-trigger" aria-label="<?= e($dcUser === null ? 'Menu tài khoản' : 'Tài khoản của ' . $dcUserName) ?>">
        <?php if ($dcAvatar !== null): ?>
          <img class="dc-shell-account-avatar" src="<?= e($dcAvatar) ?>" alt="" width="26" height="26">
        <?php elseif ($dcUser !== null): ?>
          <span class="dc-shell-account-avatar" aria-hidden="true"><?= e(initials($dcUserName)) ?></span>
        <?php else: ?>
          <?= $dcIcon('person', 18) ?>
        <?php endif; ?>
        <span class="dc-shell-account-label"><?= e($dcUser === null ? 'Tài khoản' : $dcUserName) ?></span>
        <?= $dcIcon('chevron-down', 12, 'dc-shell-chevron') ?>
      </summary>
      <div class="dc-shell-dropdown dc-shell-account">
        <?php if ($dcUser !== null): ?>
          <a href="<?= e(base_url('/account/profile')) ?>">Hồ sơ cá nhân</a>
          <a href="<?= e(base_url('/orders')) ?>"<?= $activeMenu === 'orders' ? ' aria-current="page"' : '' ?>>Đơn hàng của tôi</a>
          <?php if (is_admin()): ?>
            <a href="<?= e(base_url('/admin')) ?>">Quản trị</a>
          <?php endif; ?>
          <form action="<?= e(base_url('/logout')) ?>" method="post">
            <?= csrf_field() ?>
            <button type="submit">Đăng xuất</button>
          </form>
        <?php else: ?>
          <a href="<?= e(base_url('/login')) ?>">Đăng nhập</a>
          <a href="<?= e(base_url('/register')) ?>">Đăng ký</a>
        <?php endif; ?>
      </div>
    </details>
  </div>

  <button class="dc-shell-toggle" type="button" aria-controls="dcShellMenu"
          aria-expanded="false" aria-label="Mở menu">
    <span></span><span></span><span></span>
  </button>
</header>
<div class="dc-shell-backdrop" hidden></div>
