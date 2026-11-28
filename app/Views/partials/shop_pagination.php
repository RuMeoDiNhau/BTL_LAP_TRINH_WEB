<?php
$page = $pagination['page'];
$totalPages = $pagination['totalPages'];
if ($totalPages <= 1) {
    return;
}
$pageLink = static fn (int $number): string =>
    e(base_url($paginationPath) . '?' . http_build_query(($paginationFilters ?? []) + ['page' => $number]));
?>
<nav class="shop-pagination" aria-label="Phân trang">
  <?php if ($page > 1): ?><a class="shop-btn shop-btn--outline" href="<?= $pageLink($page - 1) ?>">Trang trước</a><?php endif; ?>
  <span>Trang <?= $page ?> / <?= $totalPages ?></span>
  <?php if ($page < $totalPages): ?><a class="shop-btn shop-btn--outline" href="<?= $pageLink($page + 1) ?>">Trang sau</a><?php endif; ?>
</nav>
