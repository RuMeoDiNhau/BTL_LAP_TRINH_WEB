<?php
/** Trang 403 cho require_admin() (member cố truy cập /admin). */
$pageTitle = '403 – Không có quyền truy cập | Decor E-Commerce';
$metaDesc = 'Tài khoản của bạn không được phép truy cập khu vực quản trị.';

ob_start();
?>
<section class="error-page">
  <h1>403 – Không có quyền truy cập</h1>
  <p>Chỉ tài khoản Quản trị viên (admin) mới được vào khu vực này.</p>
  <p><a href="<?= e(base_url('/')) ?>">Quay về trang chủ</a></p>
</section>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layouts/client.php';
