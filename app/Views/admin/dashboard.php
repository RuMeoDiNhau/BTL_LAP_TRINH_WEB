<?php
$user = $currentUser ?? current_user() ?? ['full_name' => 'Quản trị viên'];
$adminCount = (int) ($roleCounts['admin'] ?? 0);
$memberCount = (int) ($roleCounts['member'] ?? 0);
$totalUsers = $adminCount + $memberCount;

$postTotal = (int) ($postStats['total'] ?? 0);
$postPublished = (int) ($postStats['published'] ?? 0);
$postDraft = (int) ($postStats['draft'] ?? 0);

$commentTotal = (int) ($commentStats['total'] ?? 0);
$commentApproved = (int) ($commentStats['approved'] ?? 0);
$commentPending = (int) ($commentStats['pending'] ?? 0);
$commentHidden = (int) ($commentStats['hidden'] ?? 0);

$recentPosts = $recentPosts ?? [];
$recentComments = $recentComments ?? [];
$moderationOn = !empty($moderationOn);

$thumbUrl = static function (?string $path): string {
    $p = ltrim((string) $path, '/');
    return $p === '' ? asset('images/posts/post-1.svg') : base_url('/' . $p);
};
?>
<div class="ta-page-header">
  <div>
    <span class="ta-eyebrow">Decor Executive Console</span>
    <h1 class="ta-page-title">Xin chào, <?= e((string) ($user['full_name'] ?? 'Quản trị viên')) ?></h1>
    <p class="ta-page-subtitle">Theo dõi nhanh bài viết, bình luận, tài khoản và truy cập các phân hệ quản trị Decor E-Commerce.</p>
  </div>
  <div class="ta-header-buttons">
    <a href="<?= e(base_url('/admin/comments')) ?>" class="ta-btn ta-btn--outline">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      </svg>
      <span>Quản lý bình luận</span>
    </a>
    <a href="<?= e(base_url('/admin/posts/create')) ?>" class="ta-btn ta-btn--primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <line x1="12" y1="5" x2="12" y2="19"/>
        <line x1="5" y1="12" x2="19" y2="12"/>
      </svg>
      <span>Viết bài mới</span>
    </a>
  </div>
</div>

<section class="ta-kpi-grid" aria-label="Chỉ số tổng quan">
  <div class="ta-stat-card">
    <div>
      <div class="ta-stat-label">Bài viết / Tin tức</div>
      <div class="ta-stat-value"><?= str_pad((string) $postTotal, 2, '0', STR_PAD_LEFT) ?></div>
      <div class="ta-stat-caption">
        <strong><?= $postPublished ?></strong> đã xuất bản &bull; <strong><?= $postDraft ?></strong> bản nháp
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

  <div class="ta-stat-card">
    <div>
      <div class="ta-stat-label">Bình luận bài viết</div>
      <div class="ta-stat-value"><?= str_pad((string) $commentTotal, 2, '0', STR_PAD_LEFT) ?></div>
      <div class="ta-stat-caption">
        <strong><?= $commentApproved ?></strong> đã duyệt &bull;
        <?= $commentPending > 0 ? '<span style="color:#b54708;font-weight:600;">' . $commentPending . ' chờ duyệt</span>' : $commentHidden . ' đã ẩn' ?>
      </div>
    </div>
    <span class="ta-stat-icon" aria-hidden="true">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      </svg>
    </span>
  </div>

  <div class="ta-stat-card">
    <div>
      <div class="ta-stat-label">Tài khoản hệ thống</div>
      <div class="ta-stat-value"><?= str_pad((string) $totalUsers, 2, '0', STR_PAD_LEFT) ?></div>
      <div class="ta-stat-caption">
        <strong><?= $adminCount ?></strong> quản trị &bull; <strong><?= $memberCount ?></strong> thành viên
      </div>
    </div>
    <span class="ta-stat-icon" aria-hidden="true">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
      </svg>
    </span>
  </div>

  <div class="ta-stat-card">
    <div>
      <div class="ta-stat-label">Kiểm duyệt bình luận</div>
      <div class="ta-stat-value" style="font-size:22px;padding-top:4px;">
        <?= $moderationOn ? 'Đang bật' : 'Tự động' ?>
      </div>
      <div class="ta-stat-caption">
        <a href="<?= e(base_url('/admin/roles')) ?>">Xem ma trận phân quyền &rarr;</a>
      </div>
    </div>
    <span class="ta-stat-icon" aria-hidden="true">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
      </svg>
    </span>
  </div>
</section>

<div class="ta-dashboard-grid">
  <section class="ta-card" aria-label="Bài viết mới nhất">
    <div class="ta-card-header">
      <div>
        <h2 class="ta-card-title">Bài viết mới nhất</h2>
        <p class="ta-card-subtitle">5 bài viết cập nhật gần đây trong mục Tin tức &amp; Cẩm nang</p>
      </div>
      <a href="<?= e(base_url('/admin/posts')) ?>" class="ta-btn ta-btn--outline ta-btn--sm">Xem tất cả &rarr;</a>
    </div>

    <div class="ta-table-wrap">
      <table class="ta-table">
        <thead>
          <tr>
            <th>Bài viết</th>
            <th>Trạng thái</th>
            <th>Lượt xem</th>
            <th style="text-align:right;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($recentPosts)): ?>
            <tr>
              <td colspan="4" style="text-align:center;padding:28px;color:var(--ta-gray-500);">
                Chưa có bài viết nào. <a href="<?= e(base_url('/admin/posts/create')) ?>">Tạo bài viết đầu tiên</a>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($recentPosts as $p): ?>
              <?php $isPub = ($p['status'] ?? '') === 'published'; ?>
              <tr>
                <td>
                  <div class="ta-post-cell">
                    <img class="ta-post-thumb" src="<?= e($thumbUrl($p['thumbnail'] ?? '')) ?>" alt="" loading="lazy">
                    <div style="min-width:0;">
                      <div class="ta-post-title-text"><?= e((string) $p['title']) ?></div>
                      <div class="ta-post-slug-text">/posts/<?= e((string) $p['slug']) ?></div>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="ta-badge <?= $isPub ? 'ta-badge--success' : 'ta-badge--warning' ?>">
                    <?= $isPub ? 'Đã xuất bản' : 'Bản nháp' ?>
                  </span>
                </td>
                <td><?= (int) ($p['views'] ?? 0) ?></td>
                <td style="text-align:right;">
                  <a href="<?= e(base_url('/admin/posts/' . (int) $p['id'] . '/edit')) ?>" class="ta-btn ta-btn--outline ta-btn--sm">Sửa</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <div class="ta-dashboard-side">
    <section class="ta-card" aria-label="Bình luận gần đây">
      <div class="ta-card-header">
        <div>
          <h2 class="ta-card-title">Bình luận gần đây</h2>
          <p class="ta-card-subtitle">Phản hồi mới nhất từ thành viên</p>
        </div>
        <a href="<?= e(base_url('/admin/comments')) ?>" class="ta-btn ta-btn--outline ta-btn--sm">Tất cả</a>
      </div>

      <div class="ta-recent-comments">
        <?php if (empty($recentComments)): ?>
          <p class="ta-empty-mini">Chưa có bình luận nào trên hệ thống.</p>
        <?php else: ?>
          <?php foreach ($recentComments as $c): ?>
            <?php
            $cStatus = (string) ($c['status'] ?? 'approved');
            $cBadgeClass = $cStatus === 'approved' ? 'ta-badge--success' : ($cStatus === 'pending' ? 'ta-badge--warning' : 'ta-badge--muted');
            $cBadgeText = $cStatus === 'approved' ? 'Đã duyệt' : ($cStatus === 'pending' ? 'Chờ duyệt' : 'Đã ẩn');
            $cText = (string) ($c['content'] ?? '');
            if (mb_strlen($cText, 'UTF-8') > 88) {
                $cText = mb_substr($cText, 0, 88, 'UTF-8') . '...';
            }
            ?>
            <div class="ta-recent-comment">
              <div class="ta-recent-comment-head">
                <strong><?= e((string) ($c['author_name'] ?? 'Thành viên')) ?></strong>
                <span class="ta-badge <?= $cBadgeClass ?>"><?= $cBadgeText ?></span>
              </div>
              <p class="ta-recent-comment-body"><?= e($cText) ?></p>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

    <section class="ta-card" aria-label="Lối tắt nhanh các phân hệ">
      <div class="ta-card-header">
        <h2 class="ta-card-title">Truy cập nhanh</h2>
      </div>
      <div class="ta-quick-links">
        <a href="<?= e(base_url('/admin/roles')) ?>" class="ta-quick-link">Vai trò &amp; quyền hạn</a>
        <a href="<?= e(base_url('/admin/users')) ?>" class="ta-quick-link">Người dùng</a>
        <a href="<?= e(base_url('/admin/posts')) ?>" class="ta-quick-link">Bài viết / Tin tức</a>
        <a href="<?= e(base_url('/admin/comments')) ?>" class="ta-quick-link">Bình luận</a>
        <a href="<?= e(base_url('/admin/settings')) ?>" class="ta-quick-link">Nội dung trang</a>
        <a href="<?= e(base_url('/admin/faqs')) ?>" class="ta-quick-link">FAQ List</a>
      </div>
    </section>
  </div>
</div>
