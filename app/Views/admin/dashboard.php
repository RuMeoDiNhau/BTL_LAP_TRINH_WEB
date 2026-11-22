<?php
$esc = static fn (?string $s): string => function_exists('e')
    ? (string) e($s)
    : htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');

$url = static fn (string $path): string => function_exists('base_url')
    ? (string) base_url($path)
    : $path;

$adminCount = (int) ($roleCounts['admin'] ?? 0);
$memberCount = (int) ($roleCounts['member'] ?? 0);
?>
<div class="ta-page-header">
  <div>
    <h1 class="ta-page-title">Bảng điều khiển Decor Admin</h1>
    <p class="ta-page-subtitle">Truy cập nhanh các phân hệ quản trị và kiểm soát phân quyền trên hệ thống Decor E-Commerce.</p>
  </div>
</div>

<section class="ta-stats-grid" aria-label="Thống kê nhanh">
  <div class="ta-stat-card">
    <div>
      <div class="ta-stat-label">Quản trị viên (admin)</div>
      <div class="ta-stat-value"><?= str_pad((string) $adminCount, 2, '0', STR_PAD_LEFT) ?></div>
      <div class="ta-stat-caption"><a href="<?= $esc($url('/admin/roles')) ?>">Xem ma trận phân quyền &rarr;</a></div>
    </div>
    <span class="ta-stat-icon" aria-hidden="true">&#128737;</span>
  </div>

  <div class="ta-stat-card">
    <div>
      <div class="ta-stat-label">Thành viên (member)</div>
      <div class="ta-stat-value"><?= str_pad((string) $memberCount, 2, '0', STR_PAD_LEFT) ?></div>
      <div class="ta-stat-caption">Tài khoản đăng ký trên website</div>
    </div>
    <span class="ta-stat-icon" aria-hidden="true">&#128100;</span>
  </div>

  <div class="ta-stat-card">
    <div>
      <div class="ta-stat-label">Tin tức &amp; Bình luận</div>
      <div class="ta-stat-value">02</div>
      <div class="ta-stat-caption">
        <a href="<?= $esc($url('/admin/posts')) ?>">Quản lý bài viết</a> &bull;
        <a href="<?= $esc($url('/admin/comments')) ?>">Bình luận</a>
      </div>
    </div>
    <span class="ta-stat-icon" aria-hidden="true">&#128240;</span>
  </div>
</section>
