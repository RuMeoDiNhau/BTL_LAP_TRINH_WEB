<?php
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
      <div class="ta-stat-caption"><a href="<?= e(base_url('/admin/roles')) ?>">Xem ma trận phân quyền &rarr;</a></div>
    </div>
    <span class="ta-stat-icon" aria-hidden="true">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
      </svg>
    </span>
  </div>

  <div class="ta-stat-card">
    <div>
      <div class="ta-stat-label">Thành viên (member)</div>
      <div class="ta-stat-value"><?= str_pad((string) $memberCount, 2, '0', STR_PAD_LEFT) ?></div>
      <div class="ta-stat-caption">Tài khoản đăng ký trên website</div>
    </div>
    <span class="ta-stat-icon" aria-hidden="true">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
        <circle cx="12" cy="7" r="4"/>
      </svg>
    </span>
  </div>

  <div class="ta-stat-card">
    <div>
      <div class="ta-stat-label">Tin tức &amp; Bình luận</div>
      <div class="ta-stat-value">02</div>
      <div class="ta-stat-caption">
        <a href="<?= e(base_url('/admin/posts')) ?>">Quản lý bài viết</a> &bull;
        <a href="<?= e(base_url('/admin/comments')) ?>">Bình luận</a>
      </div>
    </div>
    <span class="ta-stat-icon" aria-hidden="true">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
        <line x1="16" y1="13" x2="8" y2="13"/>
        <line x1="16" y1="17" x2="8" y2="17"/>
      </svg>
    </span>
  </div>
</section>
