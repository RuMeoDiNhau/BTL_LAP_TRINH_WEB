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
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= $esc($metaDesc ?? 'Hệ thống xác thực Decor E-Commerce') ?>">
  <title><?= $esc($pageTitle ?? 'Decor E-Commerce') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= $esc($assetUrl('css/auth.css')) ?>">
</head>
<body>
  <div class="auth-layout">
    <aside class="auth-aside" aria-label="Giới thiệu hệ thống phân quyền Decor">
      <a href="<?= $esc($url('/')) ?>" class="brand-logo">
        <img src="<?= $esc($assetUrl('images/brand/logo.svg')) ?>" alt="Logo Decor" width="38" height="38">
        <span>Decor</span>
        <span class="brand-logo-sub">Studio</span>
      </a>

      <div class="aside-hero">
        <div class="aside-eyebrow">Không gian nội thất &amp; quản trị</div>
        <h1 class="aside-title">Đúng người.<br>Đúng quyền truy cập.</h1>
        <p class="aside-desc">
          Một nơi để quản lý tài khoản, kết nối đội ngũ và phân quyền rõ ràng cho từng thành viên trên hệ thống Decor.
        </p>

        <div class="role-preview-card" aria-live="polite">
          <div class="role-preview-user">
            <div class="role-preview-user-info">
              <span class="role-avatar" id="previewAvatar">MA</span>
              <div>
                <div class="role-user-name" id="previewName">Nguyễn Minh Anh</div>
                <div class="role-user-email" id="previewEmail">admin@decor.vn</div>
              </div>
            </div>
            <span class="role-verified-badge" title="Tài khoản đã xác thực">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2l2.4 2.1 3.1-.3.7 3 2.8 1.5-1.3 2.9 1.3 2.9-2.8 1.5-.7 3-3.1-.3L12 22l-2.4-2.1-3.1.3-.7-3-2.8-1.5 1.3-2.9-1.3-2.9 2.8-1.5.7-3 3.1.3L12 2z"/>
                <polyline points="9 12 11 14 15 10"/>
              </svg>
            </span>
          </div>

          <div class="role-preview-meta">
            <span>Quyền truy cập của bạn</span>
            <span class="status-dot-active">Đang hoạt động</span>
          </div>

          <div class="role-perm-pills">
            <span class="perm-pill" data-perm-pill="view">&#10003; Xem</span>
            <span class="perm-pill" data-perm-pill="create">&#10003; Tạo</span>
            <span class="perm-pill" data-perm-pill="edit">&#10003; Sửa</span>
            <span class="perm-pill" data-perm-pill="delete">&#10003; Xóa</span>
          </div>
        </div>

        <div class="role-switcher" role="group" aria-label="Xem thử quyền theo vai trò">
          <button type="button" class="role-tab-btn is-active" data-role-preview="admin" aria-pressed="true">
            Quản trị viên
          </button>
          <button type="button" class="role-tab-btn" data-role-preview="member" aria-pressed="false">
            Thành viên
          </button>
        </div>

        <div class="aside-security-note">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            <polyline points="9 12 11 14 15 10"/>
          </svg>
          <span>Quản lý tập trung. Cộng tác nhất quán.</span>
        </div>
      </div>

      <div class="aside-footer">
        &copy; 2026 Decor E-Commerce. Mọi quyền được bảo lưu.
      </div>
    </aside>

    <main class="auth-main">
      <div class="auth-topbar">
        <a href="<?= $esc($url('/')) ?>" class="brand-logo auth-topbar-mobile-logo">
          <img src="<?= $esc($assetUrl('images/brand/logo.svg')) ?>" alt="Logo Decor" width="32" height="32">
          <span>Decor</span>
        </a>
        <div>
          <span><?= $esc($topbarPrompt ?? '') ?></span>
          <a href="<?= $esc($url($topbarLink ?? '/login')) ?>"><?= $esc($topbarText ?? '') ?></a>
        </div>
      </div>

      <div class="auth-container">
        <?= $content ?? '' ?>
      </div>

      <footer class="auth-bottombar">
        <span>Hệ thống xác thực bảo mật Decor</span>
        <a href="<?= $esc($url('/')) ?>">Quay về trang chủ &#8599;</a>
      </footer>
    </main>
  </div>

  <script src="<?= $esc($assetUrl('js/auth.js')) ?>"></script>
</body>
</html>
