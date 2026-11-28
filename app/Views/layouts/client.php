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

// ===== #2 About/FAQ =====
// Các module chọn shell qua tham số useShell của view.
$useShell = !empty($useShell ?? false);
if ($useShell) {
    // Sprite icon local, kích thước cố định để CSS không làm SVG phình to.
    $dcIcon = static function (string $id, int $size = 20, string $class = ''): string {
        return '<svg class="' . e($class) . '" width="' . $size . '" height="' . $size
            . '" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="'
            . e(asset('icons/bootstrap-icons.svg#' . $id)) . '"></use></svg>';
    };

    // Footer cần phone/email/address; đọc trực tiếp bằng prepared statement, không thêm helper vào core.
    $dcFooterSettings = ['phone' => '0901234567', 'email' => 'contact@decor.vn', 'address' => ''];
    try {
        $dcStmt = Database::getInstance()->getConnection()->prepare(
            'SELECT setting_key, setting_value FROM settings WHERE setting_key IN (:k1, :k2, :k3)'
        );
        $dcStmt->bindValue(':k1', 'phone', PDO::PARAM_STR);
        $dcStmt->bindValue(':k2', 'email', PDO::PARAM_STR);
        $dcStmt->bindValue(':k3', 'address', PDO::PARAM_STR);
        $dcStmt->execute();
        foreach ($dcStmt->fetchAll() as $dcRow) {
            $dcKey = (string) $dcRow['setting_key'];
            if (array_key_exists($dcKey, $dcFooterSettings)) {
                $dcFooterSettings[$dcKey] = trim((string) $dcRow['setting_value']);
            }
        }
    } catch (Throwable $dcError) {
        // CSDL chưa sẵn sàng thì giữ giá trị mặc định ở trên.
    }
    $dcPhoneHref = 'tel:' . preg_replace('/[^0-9+]/', '', $dcFooterSettings['phone']);
}
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
<!-- ===== #2 About/FAQ: class dc-shell chỉ có khi controller truyền useShell. ===== -->
<body<?= $useShell ? ' class="dc-shell"' : '' ?>>
<?php if ($useShell): ?>
  <?php require dirname(__DIR__) . '/partials/about_faq_header.php'; ?>
<?php else: ?>
  <header class="site-header">
    <a class="site-brand" href="<?= e(base_url('/')) ?>">Decor</a>
    <nav class="site-nav" aria-label="Menu chính">
      <ul>
        <li><a href="<?= e(base_url('/')) ?>">Home</a></li>
        <li><a href="<?= e(base_url('/about')) ?>">About</a></li>
        <li><a href="<?= e(base_url('/products')) ?>">Products</a></li>
        <li><a href="<?= e(base_url('/cart')) ?>">Giỏ hàng</a></li>
        <li><a href="<?= e(base_url('/posts')) ?>">News</a></li>
        <li><a href="<?= e(base_url('/faq')) ?>">FAQs</a></li>
        <li><a href="<?= e(base_url('/contact')) ?>">Contact</a></li>
        <?php if (is_logged_in()): ?>
          <li><a href="<?= e(base_url('/account/profile')) ?>">Tài khoản</a></li>
          <li><a href="<?= e(base_url('/orders')) ?>">Đơn hàng của tôi</a></li>
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
<?php endif; ?>

  <main class="site-main">
    <?= render_flash(null, ['success' => $flashSuccess ?? null, 'error' => $flashError ?? null]) ?>
    <?= $content ?>
  </main>

<?php if ($useShell): ?>
  <?php require dirname(__DIR__) . '/partials/about_faq_footer.php'; ?>
<?php else: ?>
  <footer class="site-footer">
    <p>&copy; 2026 Decor E-Commerce. All rights reserved.</p>
  </footer>
<?php endif; ?>

  <script src="<?= e(asset('js/main.js')) ?>"></script>
  <?php foreach ($pageScripts as $script): ?>
    <script src="<?= e(asset($script)) ?>"></script>
  <?php endforeach; ?>
</body>
</html>
