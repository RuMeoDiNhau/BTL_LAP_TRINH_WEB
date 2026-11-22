<?php
$esc = static fn (?string $s): string => function_exists('e')
    ? (string) e($s)
    : htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');

$url = static fn (string $path): string => function_exists('base_url')
    ? (string) base_url($path)
    : $path;

$assetUrl = static fn (string $path): string => function_exists('asset')
    ? (string) asset($path)
    : '/assets/' . ltrim($path, '/');

$user = $currentUser ?? ($_SESSION['user'] ?? ['full_name' => 'Quản trị viên', 'role' => 'admin']);
$nameParts = preg_split('/\s+/u', trim((string) ($user['full_name'] ?? 'AD'))) ?: ['AD'];
$initials = count($nameParts) >= 2
    ? mb_strtoupper(mb_substr($nameParts[0], 0, 1, 'UTF-8') . mb_substr($nameParts[count($nameParts) - 1], 0, 1, 'UTF-8'), 'UTF-8')
    : mb_strtoupper(mb_substr($nameParts[0], 0, 2, 'UTF-8'), 'UTF-8');

$activeMenu = $activeMenu ?? '';
$pageScripts = $pageScripts ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $esc($pageTitle ?? 'Decor Admin') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= $esc($assetUrl('css/admin.css')) ?>">
</head>
<body>
  <div class="ta-shell">
    <aside class="ta-sidebar" aria-label="Menu quản trị Decor">
      <div>
        <a href="<?= $esc($url('/admin')) ?>" class="ta-logo">
          <img src="<?= $esc($assetUrl('images/brand/logo.svg')) ?>" alt="Logo Decor" width="34" height="34">
          <span>Decor</span>
        </a>

        <div class="ta-workspace-switcher">
          <span class="ta-workspace-badge">D</span>
          <div class="ta-workspace-info">
            <div class="ta-workspace-name">Không gian Decor</div>
            <div class="ta-workspace-sub">Quản trị hệ thống</div>
          </div>
        </div>

        <div class="ta-nav-section-label">Quản trị &amp; Phân quyền</div>
        <ul class="ta-nav-list">
          <li>
            <a href="<?= $esc($url('/admin')) ?>" class="ta-nav-link<?= $activeMenu === 'dashboard' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left"><span>Tổng quan</span></span>
            </a>
          </li>
          <li>
            <a href="<?= $esc($url('/admin/roles')) ?>" class="ta-nav-link<?= $activeMenu === 'roles' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left"><span>Vai trò &amp; quyền hạn</span></span>
            </a>
          </li>
        </ul>

        <div class="ta-nav-section-label">Nội dung &amp; Kinh doanh</div>
        <ul class="ta-nav-list">
          <li>
            <a href="<?= $esc($url('/admin/posts')) ?>" class="ta-nav-link<?= $activeMenu === 'posts' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left"><span>Bài viết / Tin tức</span></span>
            </a>
          </li>
          <li>
            <a href="<?= $esc($url('/admin/comments')) ?>" class="ta-nav-link<?= $activeMenu === 'comments' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left"><span>Bình luận bài viết</span></span>
            </a>
          </li>
          <li>
            <a href="<?= $esc($url('/admin/products')) ?>" class="ta-nav-link<?= $activeMenu === 'products' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left"><span>Sản phẩm</span></span>
            </a>
          </li>
          <li>
            <a href="<?= $esc($url('/admin/orders')) ?>" class="ta-nav-link<?= $activeMenu === 'orders' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left"><span>Đơn hàng</span></span>
            </a>
          </li>
          <li>
            <a href="<?= $esc($url('/admin/settings')) ?>" class="ta-nav-link<?= $activeMenu === 'settings' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left"><span>Cấu hình &amp; Liên hệ</span></span>
            </a>
          </li>
        </ul>
      </div>

      <div class="ta-sidebar-bottom">
        <div class="ta-current-user">
          <div class="ta-current-user-info">
            <span class="ta-avatar"><?= $esc($initials) ?></span>
            <div>
              <div class="ta-workspace-name"><?= $esc($user['full_name'] ?? 'Quản trị viên') ?></div>
              <div class="ta-workspace-sub"><?= ($user['role'] ?? '') === 'admin' ? 'Quản trị viên' : 'Thành viên' ?></div>
            </div>
          </div>
          <form action="<?= $esc($url('/logout')) ?>" method="post" style="margin:0;">
            <input type="hidden" name="_csrf" value="<?= $esc($csrfToken ?? ($_SESSION['csrf_token'] ?? '')) ?>">
            <button type="submit" style="background:none;border:none;color:#4640de;font-size:12.5px;font-weight:600;cursor:pointer;">Thoát</button>
          </form>
        </div>
      </div>
    </aside>

    <div class="ta-main">
      <header class="ta-topbar">
        <nav class="ta-breadcrumb" aria-label="Breadcrumb">
          <a href="<?= $esc($url('/admin')) ?>">Quản trị</a>
          <?php if (!empty($breadcrumb)): ?>
            <span>&rsaquo;</span>
            <span class="ta-breadcrumb-current"><?= $esc($breadcrumb) ?></span>
          <?php endif; ?>
        </nav>
        <div class="ta-topbar-actions">
          <a href="<?= $esc($url('/')) ?>" class="ta-btn ta-btn--outline ta-btn--sm">Xem trang khách</a>
          <span class="ta-avatar"><?= $esc($initials) ?></span>
        </div>
      </header>

      <main class="ta-content">
        <?php if (!empty($flashSuccess)): ?>
          <div class="ta-card" style="padding:12px 18px;margin-bottom:18px;border-left:4px solid #12b76a;background:#ecfdf3;color:#027a48;font-weight:600;" role="status">
            <?= $esc($flashSuccess) ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($flashError)): ?>
          <div class="ta-card" style="padding:12px 18px;margin-bottom:18px;border-left:4px solid #d92d20;background:#fef3f2;color:#b42318;font-weight:600;" role="alert">
            <?= $esc($flashError) ?>
          </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
      </main>
    </div>
  </div>

  <?php foreach ($pageScripts as $script): ?>
    <script src="<?= $esc($assetUrl($script)) ?>"></script>
  <?php endforeach; ?>
</body>
</html>
