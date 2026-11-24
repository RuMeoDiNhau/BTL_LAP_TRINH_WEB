<?php
/**
 * Layout khách tối giản để các module khác ghép nội dung vào $content.
 * TODO: nhóm thêm Bootstrap local (CSS/JS) khi có file; không dùng CDN.
 */
$pageTitle    = $pageTitle ?? 'Decor E-Commerce';
$metaDesc     = $metaDesc ?? 'Decor E-Commerce – Nội thất và giải pháp sống hiện đại.';
$metaKeywords = $metaKeywords ?? 'Decor, noi that, furniture';
$content      = $content ?? '';
$pageScripts  = $pageScripts ?? [];
$headExtra    = $headExtra ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="<?= e($metaDesc) ?>">
  <meta name="keywords" content="<?= e($metaKeywords) ?>">
  <?= $headExtra ?>
  <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body>
  <header class="site-header">
    <a class="site-brand" href="<?= e(base_url('/')) ?>">Decor</a>
    <nav class="site-nav" aria-label="Menu chính">
      <ul>
        <li><a href="<?= e(base_url('/')) ?>">Home</a></li>
        <li><a href="<?= e(base_url('/about')) ?>">About</a></li>
        <li><a href="<?= e(base_url('/products')) ?>">Products</a></li>
        <li><a href="<?= e(base_url('/posts')) ?>">News</a></li>
        <li><a href="<?= e(base_url('/faq')) ?>">FAQs</a></li>
        <li><a href="<?= e(base_url('/contact')) ?>">Contact</a></li>
        <?php if (is_logged_in()): ?>
          <li><a href="<?= e(base_url('/account/profile')) ?>">Tài khoản</a></li>
          <?php if (is_admin()): ?>
            <li><a href="<?= e(base_url('/admin')) ?>">Admin</a></li>
          <?php endif; ?>
          <li>
            <form action="<?= e(base_url('/logout')) ?>" method="post">
              <?= csrf_field() ?>
              <button type="submit">Logout</button>
            </form>
          </li>
        <?php else: ?>
          <li><a href="<?= e(base_url('/login')) ?>">Login</a></li>
          <li><a href="<?= e(base_url('/register')) ?>">Đăng ký</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>

  <main class="site-main">
    <?= render_flash(null, ['success' => $flashSuccess ?? null, 'error' => $flashError ?? null]) ?>
    <?= $content ?>
  </main>

  <footer class="site-footer">
    <p>&copy; 2026 Decor E-Commerce. All rights reserved.</p>
  </footer>

  <script src="<?= e(asset('js/main.js')) ?>"></script>
  <?php foreach ($pageScripts as $script): ?>
    <script src="<?= e(asset($script)) ?>"></script>
  <?php endforeach; ?>
</body>
</html>
