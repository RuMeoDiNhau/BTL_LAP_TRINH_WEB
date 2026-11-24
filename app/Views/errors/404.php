<?php
/** Trang 404 tự render qua layout client (được Router::notFound() require). */
$pageTitle = '404 – Không tìm thấy trang | Decor E-Commerce';
$metaDesc = 'Trang bạn yêu cầu không tồn tại trên Decor E-Commerce.';

ob_start();
?>
<section class="error-page">
  <h1>404 – Không tìm thấy trang</h1>
  <p>Đường dẫn này không tồn tại hoặc đã được di chuyển.</p>
  <p><a href="<?= e(base_url('/')) ?>">Quay về trang chủ</a></p>
</section>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layouts/client.php';
