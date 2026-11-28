<?php
$user = $currentUser ?? current_user() ?? ['full_name' => 'Quản trị viên', 'role' => 'admin'];
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
  <title><?= e($pageTitle ?? 'Decor Admin') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(asset('css/fonts.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
  <?php foreach ($pageStyles as $cssFile): ?>
    <link rel="stylesheet" href="<?= e(asset((string) $cssFile)) ?>">
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
        <a href="<?= e(base_url('/admin')) ?>" class="ta-logo">
          <span class="ta-logo-mark" aria-hidden="true">D</span>
          <span class="ta-logo-wordmark">DECOR</span>
          <span class="ta-logo-badge">Admin</span>
        </a>

        <div class="ta-workspace-switcher">
          <span class="ta-workspace-badge" aria-hidden="true">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
              <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
          </span>
          <div class="ta-workspace-info">
            <div class="ta-workspace-name">Không gian Decor</div>
            <div class="ta-workspace-sub">Quản trị hệ thống</div>
          </div>
        </div>

        <div class="ta-nav-section-label">Quản trị &amp; Phân quyền</div>
        <ul class="ta-nav-list">
          <li>
            <a href="<?= e(base_url('/admin')) ?>" class="ta-nav-link<?= $activeMenu === 'dashboard' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left">
                <svg class="ta-nav-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                  <rect x="3" y="3" width="7" height="7" rx="1"/>
                  <rect x="14" y="3" width="7" height="7" rx="1"/>
                  <rect x="14" y="14" width="7" height="7" rx="1"/>
                  <rect x="3" y="14" width="7" height="7" rx="1"/>
                </svg>
                <span>Tổng quan</span>
              </span>
            </a>
          </li>
          <li>
            <a href="<?= e(base_url('/admin/roles')) ?>" class="ta-nav-link<?= $activeMenu === 'roles' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left">
                <svg class="ta-nav-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                <span>Vai trò &amp; quyền hạn</span>
              </span>
            </a>
          </li>
          <li>
            <a href="<?= e(base_url('/admin/users')) ?>" class="ta-nav-link<?= $activeMenu === 'users' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left">
                <svg class="ta-nav-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span>Người dùng</span>
              </span>
            </a>
          </li>
        </ul>

        <div class="ta-nav-section-label">Nội dung &amp; Kinh doanh</div>
        <ul class="ta-nav-list">
          <li>
            <a href="<?= e(base_url('/admin/posts')) ?>" class="ta-nav-link<?= $activeMenu === 'posts' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left">
                <svg class="ta-nav-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14 2 14 8 20 8"/>
                  <line x1="16" y1="13" x2="8" y2="13"/>
                  <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                <span>Bài viết / Tin tức</span>
              </span>
            </a>
          </li>
          <li>
            <a href="<?= e(base_url('/admin/comments')) ?>" class="ta-nav-link<?= $activeMenu === 'comments' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left">
                <svg class="ta-nav-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                <span>Bình luận bài viết</span>
              </span>
            </a>
          </li>
          <li>
            <a href="<?= e(base_url('/admin/products')) ?>" class="ta-nav-link<?= $activeMenu === 'products' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left">
                <svg class="ta-nav-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                  <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                  <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                  <line x1="12" y1="22.08" x2="12" y2="12"/>
                </svg>
                <span>Sản phẩm</span>
              </span>
            </a>
          </li>
          <li>
            <a href="<?= e(base_url('/admin/orders')) ?>" class="ta-nav-link<?= $activeMenu === 'orders' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left">
                <svg class="ta-nav-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                  <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                  <line x1="3" y1="6" x2="21" y2="6"/>
                  <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                <span>Đơn hàng</span>
              </span>
            </a>
          </li>
          <li>
            <a href="<?= e(base_url('/admin/settings')) ?>" class="ta-nav-link<?= $activeMenu === 'settings' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left">
                <svg class="ta-nav-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                  <circle cx="12" cy="12" r="3"/>
                  <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                </svg>
                <span>Nội dung trang</span>
              </span>
            </a>
          </li>
          <li>
            <a href="<?= e(base_url('/admin/contacts')) ?>" class="ta-nav-link<?= $activeMenu === 'contacts' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left">
                <svg class="ta-nav-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                  <polyline points="22,6 12,13 2,6"/>
                </svg>
                <span>Liên hệ khách hàng</span>
              </span>
            </a>
          </li>
        </ul>

        <!-- ===== #2 About/FAQ ===== -->
        <div class="ta-nav-section-label">About / FAQ</div>
        <ul class="ta-nav-list">
          <li>
            <a href="<?= e(base_url('/admin/pages/about')) ?>" class="ta-nav-link<?= $activeMenu === 'admin-about' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left">
                <svg class="ta-nav-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                  <circle cx="12" cy="12" r="10"/>
                  <line x1="12" y1="16" x2="12" y2="12"/>
                  <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <span>About Page</span>
              </span>
            </a>
          </li>
          <li>
            <a href="<?= e(base_url('/admin/pages/faq')) ?>" class="ta-nav-link<?= $activeMenu === 'admin-faq-page' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left">
                <svg class="ta-nav-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                  <rect x="3" y="4" width="18" height="16" rx="2"/>
                  <line x1="7" y1="9" x2="17" y2="9"/>
                  <line x1="7" y1="13" x2="13" y2="13"/>
                </svg>
                <span>FAQ Page</span>
              </span>
            </a>
          </li>
          <li>
            <a href="<?= e(base_url('/admin/faqs')) ?>" class="ta-nav-link<?= $activeMenu === 'admin-faqs' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left">
                <svg class="ta-nav-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                  <circle cx="12" cy="12" r="10"/>
                  <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                  <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <span>FAQ List</span>
              </span>
            </a>
          </li>
          <li>
            <a href="<?= e(base_url('/admin/faq-questions')) ?>" class="ta-nav-link<?= $activeMenu === 'admin-faq-questions' ? ' is-active' : '' ?>">
              <span class="ta-nav-link-left">
                <svg class="ta-nav-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                  <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                </svg>
                <span>Customer Questions</span>
              </span>
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
            <span class="ta-avatar"><?= e($initials) ?></span>
            <div style="min-width:0;">
              <div class="ta-workspace-name"><?= e($user['full_name'] ?? 'Quản trị viên') ?></div>
              <div class="ta-workspace-sub"><?= ($user['role'] ?? '') === 'admin' ? 'Quản trị viên' : 'Thành viên' ?></div>
            </div>
          </div>
          <form action="<?= e(base_url('/logout')) ?>" method="post" style="margin:0;">
            <?= csrf_field() ?>
            <button type="submit" class="ta-logout-btn" title="Đăng xuất" aria-label="Đăng xuất">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
              </svg>
            </button>
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
          <a href="<?= e(base_url('/admin')) ?>">Quản trị</a>
          <?php if (!empty($breadcrumb)): ?>
            <span>&rsaquo;</span>
            <span class="ta-breadcrumb-current"><?= e($breadcrumb) ?></span>
          <?php endif; ?>
        </nav>
        </div>
        <div class="ta-topbar-actions">
          <a href="<?= e(base_url('/')) ?>" class="ta-btn ta-btn--outline ta-btn--sm">
            <span>Xem trang khách</span>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
              <polyline points="15 3 21 3 21 9"/>
              <line x1="10" y1="14" x2="21" y2="3"/>
            </svg>
          </a>
          <span class="ta-avatar"><?= e($initials) ?></span>
        </div>
      </header>

      <main class="ta-content">
        <?php if (!empty($flashSuccess)): ?>
          <div class="ta-card" style="padding:12px 18px;margin-bottom:18px;border-left:4px solid #12b76a;background:#ecfdf3;color:#027a48;font-weight:600;" role="status">
            <?= e($flashSuccess) ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($flashError)): ?>
          <div class="ta-card" style="padding:12px 18px;margin-bottom:18px;border-left:4px solid #d92d20;background:#fef3f2;color:#b42318;font-weight:600;" role="alert">
            <?= e($flashError) ?>
          </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
      </main>
    </div>
  </div>

  <?php foreach ($pageScripts as $script): ?>
    <script src="<?= e(asset($script)) ?>"></script>
  <?php endforeach; ?>
</body>
</html>
