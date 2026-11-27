<?php
$thumbUrl = static function (?string $path): string {
    $p = ltrim((string) $path, '/');
    return $p === '' ? asset('images/posts/post-1.svg') : base_url('/' . $p);
};

$buildPageUrl = static function (int $targetPage) use ($keyword, $status): string {
    $params = [];
    if ($keyword !== '') {
        $params['keyword'] = $keyword;
    }
    if ($status !== '') {
        $params['status'] = $status;
    }
    if ($targetPage > 1) {
        $params['page'] = $targetPage;
    }
    $query = http_build_query($params);
    return base_url('/admin/posts') . ($query !== '' ? '?' . $query : '');
};
?>
<div class="ta-page-header">
  <div>
    <h1 class="ta-page-title">Quản lý Tin tức / Bài viết</h1>
    <p class="ta-page-subtitle">Tìm kiếm, thêm mới, chỉnh sửa nội dung SEO và quản lý trạng thái xuất bản bài viết.</p>
  </div>
  <div class="ta-header-buttons">
    <a href="<?= e(base_url('/admin/posts/create')) ?>" class="ta-btn ta-btn--primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <line x1="12" y1="5" x2="12" y2="19"/>
        <line x1="5" y1="12" x2="19" y2="12"/>
      </svg>
      <span>Thêm bài viết mới</span>
    </a>
  </div>
</div>

<section class="ta-stats-grid" aria-label="Thống kê bài viết">
  <div class="ta-stat-card">
    <div>
      <div class="ta-stat-label">Tổng bài viết</div>
      <div class="ta-stat-value"><?= str_pad((string) $totalAll, 2, '0', STR_PAD_LEFT) ?></div>
      <div class="ta-stat-caption">Trong bảng <code>posts</code></div>
    </div>
    <span class="ta-stat-icon" aria-hidden="true">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
      </svg>
    </span>
  </div>

  <div class="ta-stat-card">
    <div>
      <div class="ta-stat-label">Đã xuất bản (published)</div>
      <div class="ta-stat-value"><?= str_pad((string) $totalPublished, 2, '0', STR_PAD_LEFT) ?></div>
      <div class="ta-stat-caption">Hiển thị công khai</div>
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
      <div class="ta-stat-label">Bản nháp (draft)</div>
      <div class="ta-stat-value"><?= str_pad((string) $totalDraft, 2, '0', STR_PAD_LEFT) ?></div>
      <div class="ta-stat-caption">Đang chờ biên tập</div>
    </div>
    <span class="ta-stat-icon" aria-hidden="true">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
      </svg>
    </span>
  </div>
</section>

<section class="ta-card" aria-label="Danh sách bài viết">
  <form class="ta-toolbar" action="<?= e(base_url('/admin/posts')) ?>" method="get">
    <div class="ta-toolbar-filters">
      <div class="ta-search-box">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
          <circle cx="11" cy="11" r="8"/>
          <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="search" name="keyword" class="ta-input" value="<?= e($keyword) ?>" placeholder="Tìm theo tiêu đề hoặc slug..." aria-label="Tìm bài viết">
      </div>

      <select name="status" class="ta-select" aria-label="Lọc trạng thái">
        <option value="">Tất cả trạng thái</option>
        <option value="published"<?= $status === 'published' ? ' selected' : '' ?>>Đã xuất bản (published)</option>
        <option value="draft"<?= $status === 'draft' ? ' selected' : '' ?>>Bản nháp (draft)</option>
      </select>

      <button type="submit" class="ta-btn ta-btn--outline ta-btn--sm" style="height:40px;padding:0 14px;">Lọc</button>
      <?php if ($keyword !== '' || $status !== ''): ?>
        <a href="<?= e(base_url('/admin/posts')) ?>" class="ta-btn ta-btn--outline ta-btn--sm" style="height:40px;padding:0 14px;">Xóa lọc</a>
      <?php endif; ?>
    </div>
    <span class="ta-result-count"><?= (int) $totalFiltered ?> kết quả</span>
  </form>

  <div class="ta-table-wrap">
    <table class="ta-table">
      <thead>
        <tr>
          <th>Bài viết</th>
          <th>Tác giả</th>
          <th>Trạng thái</th>
          <th>Lượt xem</th>
          <th>Ngày tạo</th>
          <th style="text-align:right;">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($posts)): ?>
          <tr>
            <td colspan="6" style="text-align:center;padding:28px;color:#667085;">Chưa có bài viết nào phù hợp.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($posts as $p): ?>
            <tr>
              <td>
                <div class="ta-post-cell">
                  <img class="ta-post-thumb" src="<?= e($thumbUrl($p['thumbnail'] ?? '')) ?>" alt="<?= e((string) $p['title']) ?>">
                  <div>
                    <div class="ta-post-title"><?= e((string) $p['title']) ?></div>
                    <div class="ta-post-slug">/posts/<?= e((string) $p['slug']) ?></div>
                  </div>
                </div>
              </td>
              <td><?= e((string) ($p['author_name'] ?? 'Admin')) ?></td>
              <td>
                <?php if (($p['status'] ?? '') === 'published'): ?>
                  <span class="ta-badge ta-badge--published">Đã xuất bản</span>
                <?php else: ?>
                  <span class="ta-badge ta-badge--draft">Bản nháp</span>
                <?php endif; ?>
              </td>
              <td><?= (int) $p['views'] ?></td>
              <td><?= e(date('d/m/Y', strtotime((string) $p['created_at']))) ?></td>
              <td style="text-align:right;">
                <div class="ta-row-actions">
                  <?php if (($p['status'] ?? '') === 'published'): ?>
                    <a href="<?= e(base_url('/posts/' . rawurlencode((string) $p['slug']))) ?>" class="ta-btn ta-btn--outline ta-btn--sm" target="_blank">Xem</a>
                  <?php endif; ?>
                  <a href="<?= e(base_url('/admin/posts/' . (int) $p['id'] . '/edit')) ?>" class="ta-btn ta-btn--outline ta-btn--sm">Sửa</a>
                  <form action="<?= e(base_url('/admin/posts/' . (int) $p['id'] . '/delete')) ?>" method="post" class="js-confirm-delete" data-confirm-msg="Bạn có chắc muốn xóa bài viết này?" style="margin:0;">
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
      <span>Trang <?= (int) $page ?> / <?= (int) $totalPages ?> (Tổng <?= (int) $totalFiltered ?> bài viết)</span>
      <nav class="ta-pagination" aria-label="Phân trang bài viết">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a href="<?= e($buildPageUrl($i)) ?>" class="ta-page-btn<?= $i === $page ? ' is-active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
      </nav>
    </div>
  <?php endif; ?>
</section>
