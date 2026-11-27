<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e($metaDesc ?? 'Hệ thống xác thực Decor E-Commerce') ?>">
  <title><?= e($pageTitle ?? 'Decor E-Commerce') ?></title>
  <link rel="stylesheet" href="<?= e(asset('css/fonts.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/auth.css')) ?>">
</head>
<body>
  <div class="auth-layout">
    <aside class="auth-aside" aria-label="Không gian thương hiệu Decor">
      <img
        class="auth-aside-bg"
        src="<?= e(base_url('/decor-template/decor/images/about.jpg')) ?>"
        alt=""
        aria-hidden="true"
      >
      <div class="auth-aside-scrim" aria-hidden="true"></div>

      <div class="auth-aside-inner">
        <a href="<?= e(base_url('/')) ?>" class="brand-wordmark brand-wordmark--light">
          <span class="brand-wordmark-name">DECOR</span>
          <span class="brand-wordmark-tag">Nội thất &amp; Không gian</span>
        </a>

        <div class="aside-editorial">
          <p class="aside-eyebrow">Nghệ thuật bài trí không gian sống</p>
          <h1 class="aside-title">Tinh tế trong từng đường nét nội thất.</h1>
          <p class="aside-desc">
            Đăng nhập để lưu sản phẩm yêu thích, theo dõi đơn hàng và chia sẻ cảm nhận cùng cộng đồng yêu nội thất Decor.
          </p>

          <ul class="aside-highlights">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
              <span>Bộ sưu tập nội thất chọn lọc &amp; xu hướng bài trí mới nhất</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
              <span>Theo dõi đơn hàng, lịch sử mua sắm và bình luận bài viết</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
              <span>Phiên đăng nhập bảo mật, phân quyền rõ ràng trên toàn hệ thống</span>
            </li>
          </ul>
        </div>

        <div class="aside-footer">
          <span>&copy; 2026 Decor E-Commerce</span>
          <span aria-hidden="true">&middot;</span>
          <span>Hà Nội, Việt Nam</span>
        </div>
      </div>
    </aside>

    <main class="auth-main">
      <header class="auth-topbar">
        <a href="<?= e(base_url('/')) ?>" class="brand-wordmark auth-topbar-mobile-logo">
          <span class="brand-wordmark-name">DECOR</span>
        </a>
        <div class="auth-topbar-switch">
          <span><?= e($topbarPrompt ?? '') ?></span>
          <a href="<?= e(base_url($topbarLink ?? '/login')) ?>"><?= e($topbarText ?? '') ?></a>
        </div>
      </header>

      <div class="auth-container">
        <div class="auth-card">
          <?= $content ?? '' ?>
        </div>
      </div>

      <footer class="auth-bottombar">
        <span>Hệ thống xác thực bảo mật Decor</span>
        <a href="<?= e(base_url('/')) ?>">Quay về trang chủ &#8599;</a>
      </footer>
    </main>
  </div>

  <script src="<?= e(asset('js/auth.js')) ?>"></script>
</body>
</html>
