<?php
/** ===== #2 About/FAQ ===== Header riêng, chỉ render khi $useShell = true. */
// ===== #2 About/FAQ: menu mobile dạng drawer, aria-expanded do about-faq.js cập nhật.
// Route chưa tồn tại -> render chữ, không tự thêm route.
$dcHeaderNav = [
    ['Home', '/'],
    ['About', '/about'],
    ['Products', null],
    ['Pricing', null],
    ['News', null],
    ['FAQs', '/faq'],
    ['Contact', null],
];
$dcNavKey = ['/' => 'home', '/about' => 'about', '/faq' => 'faq'];
?>
<header class="dc-shell-header">
  <a class="dc-shell-logo" href="<?= e(base_url('/')) ?>">Decor</a>

  <!-- hidden trên mobile; CSS min-width:992px luôn hiện nav để desktop không phụ thuộc JS. -->
  <nav id="dcShellMenu" class="dc-shell-nav" aria-label="Menu chính" hidden>
    <ul>
      <?php foreach ($dcHeaderNav as $dcItem): ?>
        <?php
        $dcLabel = (string) $dcItem[0];
        $dcPath = $dcItem[1] === null ? null : (string) $dcItem[1];
        $dcKey = $dcPath === null ? '' : (string) ($dcNavKey[$dcPath] ?? '');
        ?>
        <?php if ($dcPath === null): ?>
          <li><span class="dc-shell-nav__text"><?= e($dcLabel) ?></span></li>
        <?php else: ?>
          <li>
            <a href="<?= e(base_url($dcPath)) ?>"<?= $dcKey !== '' && $activeMenu === $dcKey ? ' class="is-active" aria-current="page"' : '' ?>><?= e($dcLabel) ?></a>
          </li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  </nav>

  <div class="dc-shell-actions">
    <details class="dc-shell-menu">
      <summary class="dc-shell-icon" aria-label="Mở tìm kiếm"><?= $dcIcon('search', 18) ?></summary>
      <div class="dc-shell-dropdown dc-shell-search">
        <form role="search" method="get" action="<?= e(url('faq')) ?>">
          <label class="dc-shell-visually-hidden" for="dcShellSearch">Tìm kiếm</label>
          <input id="dcShellSearch" type="search" name="s" placeholder="Nhập và nhấn Enter" autocomplete="off">
          <button type="submit" aria-label="Gửi tìm kiếm"><?= $dcIcon('search', 16) ?></button>
        </form>
      </div>
    </details>

    <details class="dc-shell-menu">
      <summary class="dc-shell-icon" aria-label="Menu tài khoản">
        <?= $dcIcon('person', 18) ?><?= $dcIcon('chevron-down', 12, 'dc-shell-chevron') ?>
      </summary>
      <div class="dc-shell-dropdown dc-shell-account">
        <?php if (is_logged_in()): ?>
          <a href="<?= e(base_url('/account/profile')) ?>">Tài khoản</a>
          <?php if (is_admin()): ?>
            <a href="<?= e(base_url('/admin')) ?>">Admin</a>
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
