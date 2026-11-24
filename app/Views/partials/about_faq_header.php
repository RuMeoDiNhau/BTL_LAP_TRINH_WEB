<?php
/** ===== #2 About/FAQ ===== Header riêng, chỉ render khi $useShell = true. */
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

  <nav class="dc-shell-nav" aria-label="Main navigation">
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
      <summary class="dc-shell-icon" aria-label="Open search"><?= $dcIcon('search', 18) ?></summary>
      <div class="dc-shell-dropdown dc-shell-search">
        <form role="search" method="get" action="<?= e(url('faq')) ?>">
          <label class="dc-shell-visually-hidden" for="dcShellSearch">Search</label>
          <input id="dcShellSearch" type="search" name="s" placeholder="Type and press enter" autocomplete="off">
          <button type="submit" aria-label="Submit search"><?= $dcIcon('search', 16) ?></button>
        </form>
      </div>
    </details>

    <details class="dc-shell-menu">
      <summary class="dc-shell-icon" aria-label="Account menu">
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
            <button type="submit">Logout</button>
          </form>
        <?php else: ?>
          <a href="<?= e(base_url('/login')) ?>">Login</a>
          <a href="<?= e(base_url('/register')) ?>">Đăng ký</a>
        <?php endif; ?>
      </div>
    </details>
  </div>
</header>
