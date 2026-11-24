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
$pageStyles = $pageStyles ?? [];
$unreadCount = isset($unreadCount) ? (int) $unreadCount : 0;

// ===== #2 About/FAQ =====
// TailAdmin dùng chung style.css + bundle.js cho MỌI trang (xem AGENTS.md template).
// Nạp ở đây để drawer mobile hoạt động cả ở trang admin không phải #2; array_unique
// tránh load lại bundle.js khi controller #2 đã truyền sẵn (Alpine.start() chỉ chạy 1 lần).
// admin-extra.css nạp sau admin/style.css để bridge với admin.css unlayered của nhóm #1.
$pageStyles = array_values(array_unique(array_merge(
    ['admin/style.css', 'css/admin-extra.css'],
    $pageStyles
)));
$pageScripts = array_values(array_unique(array_merge(
    ['admin/bundle.js'],
    $pageScripts
)));
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
  <?php foreach ($pageStyles as $cssFile): ?>
    <link rel="stylesheet" href="<?= $esc($assetUrl((string) $cssFile)) ?>">
  <?php endforeach; ?>
</head>
<!-- ===== #2 About/FAQ ===== body giữ trạng thái sidebarToggle như TailAdmin app-layout. -->
<body x-data="{ sidebarToggle: false }">
  <div class="ta-shell">
    <!-- ===== #2 About/FAQ ===== Theo template: backdrop + sidebar dạng drawer trên mobile. -->
    <div x-cloak x-show="sidebarToggle" x-transition.opacity
         class="ta-backdrop fixed inset-0 z-99 bg-gray-900/50 backdrop-blur-xs xl:hidden"
         aria-hidden="true" @click="sidebarToggle = false"></div>

    <aside x-cloak
           :class="sidebarToggle ? 'ta-sidebar-open translate-x-0' : '-translate-x-full'"
           class="ta-sidebar fixed top-0 left-0 z-9999 flex h-screen w-72.5 flex-col border-r border-gray-200 bg-white px-5 transition-all duration-300 xl:static xl:translate-x-0"
           @click.outside="if (window.innerWidth < 1280) sidebarToggle = false"
           aria-label="Menu quản trị Decor">
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
          <li>
            <a href="<?= $esc($url('/admin/users')) ?>" class="ta-nav-link<?= $activeMenu === 'users' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left"><span>Người dùng</span></span>
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

        <!-- ===== #2 About/FAQ ===== -->
        <div class="ta-nav-section-label">About / FAQ</div>
        <ul class="ta-nav-list">
          <li>
            <a href="<?= $esc($url('/admin/pages/about')) ?>" class="ta-nav-link<?= $activeMenu === 'admin-about' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left"><span>About Page</span></span>
            </a>
          </li>
          <li>
            <a href="<?= $esc($url('/admin/pages/faq')) ?>" class="ta-nav-link<?= $activeMenu === 'admin-faq-page' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left"><span>FAQ Page</span></span>
            </a>
          </li>
          <li>
            <a href="<?= $esc($url('/admin/faqs')) ?>" class="ta-nav-link<?= $activeMenu === 'admin-faqs' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left"><span>FAQ List</span></span>
            </a>
          </li>
          <li>
            <a href="<?= $esc($url('/admin/faq-questions')) ?>" class="ta-nav-link<?= $activeMenu === 'admin-faq-questions' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left"><span>Customer Questions</span></span>
              <?php if ($unreadCount > 0): ?>
                <span class="ta-nav-badge" aria-label="<?= $unreadCount ?> câu hỏi chưa đọc"><?= $unreadCount ?></span>
              <?php endif; ?>
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
        <!-- ===== #2 About/FAQ ===== Gom toggle + breadcrumb thành 1 nhóm trái như header/index.html của template. -->
        <div class="ta-topbar-left">
        <!-- ===== #2 About/FAQ ===== Nút Toggle Sidebar theo TailAdmin app-layout/header/index.html. -->
        <button type="button" class="ta-sidebar-toggle inline-flex size-10 shrink-0 items-center justify-center rounded-[10px] border border-gray-300 bg-transparent text-gray-500 shadow-theme-xs transition-all select-none hover:bg-gray-50 hover:text-gray-800 focus-visible:ring-3 focus-visible:ring-gray-300/50 focus-visible:outline-hidden active:translate-y-px lg:size-11 xl:hidden dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700/50 dark:hover:text-white"
                @click.stop="sidebarToggle = !sidebarToggle"
                :aria-expanded="sidebarToggle ? 'true' : 'false'"
                aria-label="Mở hoặc đóng menu quản trị">
          <svg x-show="sidebarToggle && window.innerWidth < 1280" class="size-6"
               xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
               stroke-linejoin="round" aria-hidden="true">
            <path d="M18 6L6 18M6 6l12 12"></path>
          </svg>
          <svg x-show="!sidebarToggle || window.innerWidth >= 1280" class="size-auto! rtl:-scale-x-100"
               xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12"
               fill="none" aria-hidden="true">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.583252 1C0.583252 0.585788 0.919038 0.25 1.33325 0.25H14.6666C15.0808 0.25 15.4166 0.585786 15.4166 1C15.4166 1.41421 15.0808 1.75 14.6666 1.75L1.33325 1.75C0.919038 1.75 0.583252 1.41422 0.583252 1ZM0.583252 11C0.583252 10.5858 0.919038 10.25 1.33325 10.25L14.6666 10.25C15.0808 10.25 15.4166 10.5858 15.4166 11C15.4166 11.4142 15.0808 11.75 14.6666 11.75L1.33325 11.75C0.919038 11.75 0.583252 11.4142 0.583252 11ZM1.33325 5.25C0.919038 5.25 0.583252 5.58579 0.583252 6C0.583252 6.41421 0.919038 6.75 1.33325 6.75L7.99992 6.75C8.41413 6.75 8.74992 6.41421 8.74992 6C8.74992 5.58579 8.41413 5.25 7.99992 5.25L1.33325 5.25Z" fill="currentColor"></path>
          </svg>
        </button>
        <nav class="ta-breadcrumb" aria-label="Breadcrumb">
          <a href="<?= $esc($url('/admin')) ?>">Quản trị</a>
          <?php if (!empty($breadcrumb)): ?>
            <span>&rsaquo;</span>
            <span class="ta-breadcrumb-current"><?= $esc($breadcrumb) ?></span>
          <?php endif; ?>
        </nav>
        </div>
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
