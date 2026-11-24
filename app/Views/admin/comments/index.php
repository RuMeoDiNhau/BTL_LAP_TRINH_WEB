<?php
$buildPageUrl = static function (int $targetPage) use ($keyword, $status, $postId): string {
    $params = [];
    if ($keyword !== '') {
        $params['keyword'] = $keyword;
    }
    if ($status !== '') {
        $params['status'] = $status;
    }
    if ($postId > 0) {
        $params['post_id'] = $postId;
    }
    if ($targetPage > 1) {
        $params['page'] = $targetPage;
    }
    $query = http_build_query($params);
    return base_url('/admin/comments') . ($query !== '' ? '?' . $query : '');
};
?>
<div class="ta-page-header">
  <div>
    <h1 class="ta-page-title">Quản lý Bình luận bài viết</h1>
    <p class="ta-page-subtitle">Kiểm duyệt bình luận của thành viên: duyệt hiển thị, ẩn bình luận vi phạm hoặc xóa vĩnh viễn.</p>
  </div>
</div>

<section class="ta-stats-grid" aria-label="Thống kê bình luận">
  <div class="ta-stat-card">
    <div>
      <div class="ta-stat-label">Tổng bình luận</div>
      <div class="ta-stat-value"><?= str_pad((string) $totalAll, 2, '0', STR_PAD_LEFT) ?></div>
      <div class="ta-stat-caption">Tất cả bài viết</div>
    </div>
    <span class="ta-stat-icon" aria-hidden="true">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      </svg>
    </span>
  </div>

  <div class="ta-stat-card">
    <div>
      <div class="ta-stat-label">Đã duyệt (approved)</div>
      <div class="ta-stat-value"><?= str_pad((string) $totalApproved, 2, '0', STR_PAD_LEFT) ?></div>
      <div class="ta-stat-caption">Đang hiển thị công khai</div>
    </div>
    <span class="ta-stat-icon" aria-hidden="true">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
        <polyline points="22 4 12 14.01 9 11.01"/>
      </svg>
    </span>
  </div>

  <div class="ta-stat-card">
    <div>
      <div class="ta-stat-label">Chờ duyệt / Đã ẩn</div>
      <div class="ta-stat-value"><?= str_pad((string) ($totalPending + $totalHidden), 2, '0', STR_PAD_LEFT) ?></div>
      <div class="ta-stat-caption"><?= (int) $totalPending ?> chờ duyệt &bull; <?= (int) $totalHidden ?> đã ẩn</div>
    </div>
    <span class="ta-stat-icon" aria-hidden="true">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
        <circle cx="12" cy="12" r="10"/>
        <polyline points="12 6 12 12 16 14"/>
      </svg>
    </span>
  </div>
</section>

<section class="ta-card ta-moderation-bar" aria-label="Cấu hình kiểm duyệt bình luận">
  <div>
    <div style="font-weight:700;color:#101828;">Chế độ kiểm duyệt bình luận (<code>settings.comment_moderation</code>)</div>
    <div style="font-size:12.5px;color:#667085;">
      <?php if ($moderationOn): ?>
        Đang BẬT (1): Bình luận mới lưu trạng thái <strong>pending</strong>, chỉ hiển thị sau khi Admin duyệt.
      <?php else: ?>
        Đang TẮT (0): Bình luận mới của thành viên sẽ được duyệt tự động (<strong>approved</strong>) và hiển thị ngay.
      <?php endif; ?>
    </div>
  </div>
  <form id="moderationForm" action="<?= e(base_url('/admin/comments/moderation')) ?>" method="post" style="margin:0;">
    <?= csrf_field() ?>
    <input type="hidden" name="enabled" value="<?= $moderationOn ? '0' : '1' ?>">
    <button type="submit" class="ta-btn <?= $moderationOn ? 'ta-btn--outline' : 'ta-btn--primary' ?> ta-btn--sm">
      <?= $moderationOn ? 'Tắt kiểm duyệt' : 'Bật kiểm duyệt' ?>
    </button>
  </form>
</section>

<section class="ta-card" aria-label="Danh sách bình luận">
  <form class="ta-toolbar" action="<?= e(base_url('/admin/comments')) ?>" method="get">
    <div class="ta-toolbar-filters">
      <div class="ta-search-box">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
          <circle cx="11" cy="11" r="8"/>
          <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="search" name="keyword" class="ta-input" value="<?= e($keyword) ?>" placeholder="Tìm theo nội dung, người gửi hoặc bài viết..." aria-label="Tìm kiếm bình luận">
      </div>

      <select name="status" class="ta-select" aria-label="Lọc trạng thái bình luận">
        <option value="">Tất cả trạng thái</option>
        <option value="pending"<?= $status === 'pending' ? ' selected' : '' ?>>Chờ duyệt (pending)</option>
        <option value="approved"<?= $status === 'approved' ? ' selected' : '' ?>>Đã duyệt (approved)</option>
        <option value="hidden"<?= $status === 'hidden' ? ' selected' : '' ?>>Đã ẩn (hidden)</option>
      </select>

      <select name="post_id" class="ta-select" aria-label="Lọc theo bài viết">
        <option value="0">Tất cả bài viết</option>
        <?php foreach ($allPosts as $ap): ?>
          <option value="<?= (int) $ap['id'] ?>"<?= (int) $ap['id'] === (int) $postId ? ' selected' : '' ?>>
            <?= e((string) $ap['title']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <button type="submit" class="ta-btn ta-btn--outline ta-btn--sm" style="height:40px;padding:0 14px;">Lọc</button>
      <?php if ($keyword !== '' || $status !== '' || $postId > 0): ?>
        <a href="<?= e(base_url('/admin/comments')) ?>" class="ta-btn ta-btn--outline ta-btn--sm" style="height:40px;padding:0 14px;">Xóa lọc</a>
      <?php endif; ?>
    </div>
    <span class="ta-result-count"><?= (int) $totalFiltered ?> kết quả</span>
  </form>

  <div class="ta-table-wrap">
    <table class="ta-table">
      <thead>
        <tr>
          <th>Người bình luận</th>
          <th>Nội dung</th>
          <th>Bài viết</th>
          <th>Trạng thái</th>
          <th>Thời gian</th>
          <th style="text-align:right;">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($comments)): ?>
          <tr>
            <td colspan="6" style="text-align:center;padding:28px;color:#667085;">Chưa có bình luận nào phù hợp.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($comments as $c): ?>
            <tr>
              <td>
                <div style="font-weight:700;color:#101828;"><?= e((string) $c['author_name']) ?></div>
                <div style="font-size:12px;color:#667085;"><?= e((string) $c['author_email']) ?></div>
              </td>
              <td style="max-width:300px;"><?= nl2br(e((string) $c['content'])) ?></td>
              <td style="max-width:220px;">
                <a href="<?= e(base_url('/posts/' . rawurlencode((string) $c['post_slug']))) ?>" target="_blank">
                  <?= e((string) $c['post_title']) ?>
                </a>
              </td>
              <td>
                <?php if ($c['status'] === 'approved'): ?>
                  <span class="ta-badge ta-badge--approved">Đã duyệt</span>
                <?php elseif ($c['status'] === 'pending'): ?>
                  <span class="ta-badge ta-badge--pending">Chờ duyệt</span>
                <?php else: ?>
                  <span class="ta-badge ta-badge--hidden">Đã ẩn</span>
                <?php endif; ?>
              </td>
              <td style="white-space:nowrap;"><?= e(date('d/m/Y H:i', strtotime((string) $c['created_at']))) ?></td>
              <td style="text-align:right;">
                <div class="ta-row-actions">
                  <?php if ($c['status'] !== 'approved'): ?>
                    <form action="<?= e(base_url('/admin/comments/' . (int) $c['id'] . '/status')) ?>" method="post" style="margin:0;">
                      <?= csrf_field() ?>
                      <input type="hidden" name="status" value="approved">
                      <button type="submit" class="ta-btn ta-btn--success ta-btn--sm">Duyệt</button>
                    </form>
                  <?php endif; ?>
                  <?php if ($c['status'] !== 'hidden'): ?>
                    <form action="<?= e(base_url('/admin/comments/' . (int) $c['id'] . '/status')) ?>" method="post" style="margin:0;">
                      <?= csrf_field() ?>
                      <input type="hidden" name="status" value="hidden">
                      <button type="submit" class="ta-btn ta-btn--outline ta-btn--sm">Ẩn</button>
                    </form>
                  <?php endif; ?>
                  <form action="<?= e(base_url('/admin/comments/' . (int) $c['id'] . '/delete')) ?>" method="post" class="js-confirm-delete" data-confirm-msg="Bạn có chắc muốn xóa vĩnh viễn bình luận này?" style="margin:0;">
                    <?= csrf_field() ?>
                    <button type="submit" class="ta-btn ta-btn--danger ta-btn--sm">Xóa</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($totalPages > 1): ?>
    <div class="ta-card-footer">
      <span>Trang <?= (int) $page ?> / <?= (int) $totalPages ?> (Tổng <?= (int) $totalFiltered ?> bình luận)</span>
      <nav class="ta-pagination" aria-label="Phân trang bình luận">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a href="<?= e($buildPageUrl($i)) ?>" class="ta-page-btn<?= $i === $page ? ' is-active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
      </nav>
    </div>
  <?php endif; ?>
</section>
