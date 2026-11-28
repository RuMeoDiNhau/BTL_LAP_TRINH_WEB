<?php
$thumbUrl = static function (?string $path): string {
    $p = ltrim((string) $path, '/');
    if ($p === '') {
        return asset('images/posts/post-1.svg');
    }
    return base_url('/' . $p);
};

$buildPageUrl = static function (int $targetPage) use ($keyword): string {
    $params = [];
    if ($keyword !== '') {
        $params['keyword'] = $keyword;
    }
    if ($targetPage > 1) {
        $params['page'] = $targetPage;
    }
    $query = http_build_query($params);
    return base_url('/posts') . ($query !== '' ? '?' . $query : '');
};
?>
<section class="posts-front posts-shell">
  <header class="posts-hero">
    <div class="posts-hero__copy">
      <nav class="posts-crumbs" aria-label="Đường dẫn">
        <a href="<?= e(base_url('/')) ?>">Trang chủ</a>
        <span aria-hidden="true">/</span>
        <span class="posts-crumbs__current" aria-current="page">Tin tức</span>
      </nav>
      <h1>Tin tức &amp;<br> cẩm nang nội thất</h1>
      <p class="posts-hero__subtitle">
        Khám phá bí quyết bài trí không gian sống tinh tế, lựa chọn chất liệu tự nhiên và xu hướng ánh sáng hiện đại.
      </p>
    </div>
    <img class="posts-hero__image" src="<?= e(base_url('/decor-template/decor/images/banner-large-2.jpg')) ?>" alt="" fetchpriority="high">
  </header>

  <form class="posts-filters" action="<?= e(base_url('/posts')) ?>" method="get" role="search">
    <label class="posts-field posts-field--grow" for="postSearchInput">
      <span>Tìm bài viết</span>
      <input
        type="search"
        id="postSearchInput"
        name="keyword"
        maxlength="100"
        value="<?= e($keyword) ?>"
        placeholder="Ví dụ: sofa, gỗ sồi, phòng ngủ, kệ sách..."
      >
    </label>
    <button type="submit" class="posts-btn">Tìm kiếm</button>
    <?php if ($keyword !== ''): ?>
      <a href="<?= e(base_url('/posts')) ?>" class="posts-btn posts-btn--outline">Xóa bộ lọc</a>
    <?php endif; ?>
  </form>

  <div class="posts-section-head">
    <h2><?= $keyword === '' ? 'Bài viết nổi bật' : 'Kết quả cho "' . e($keyword) . '"' ?></h2>
    <span class="posts-muted"><?= (int) $totalPosts ?> bài viết</span>
  </div>

  <div class="posts-layout">
    <div>
      <?php if (empty($posts)): ?>
        <div class="posts-empty">
          <h3>Không tìm thấy bài viết phù hợp</h3>
          <p>Hãy thử tìm với từ khóa khác hoặc <a class="posts-link" href="<?= e(base_url('/posts')) ?>">xem tất cả bài viết</a>.</p>
        </div>
      <?php else: ?>
        <div class="posts-grid">
          <?php foreach ($posts as $post): ?>
            <?php $detailHref = base_url('/posts/' . rawurlencode((string) $post['slug'])); ?>
            <article class="post-card">
              <a href="<?= e($detailHref) ?>" class="post-card-media">
                <img
                  src="<?= e($thumbUrl($post['thumbnail'] ?? '')) ?>"
                  alt="<?= e((string) $post['title']) ?>"
                  loading="lazy"
                >
              </a>
              <div class="post-card-body">
                <div class="post-meta-row">
                  <span class="post-meta-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                      <rect x="3" y="4" width="18" height="18" rx="2"/>
                      <line x1="16" y1="2" x2="16" y2="6"/>
                      <line x1="8" y1="2" x2="8" y2="6"/>
                      <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <time datetime="<?= e(date('Y-m-d', strtotime((string) $post['created_at']))) ?>">
                      <?= e(date('d/m/Y', strtotime((string) $post['created_at']))) ?>
                    </time>
                  </span>
                  <span class="post-meta-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <?= (int) $post['views'] ?> lượt xem
                  </span>
                </div>

                <h3 class="post-card-title">
                  <a href="<?= e($detailHref) ?>"><?= e((string) $post['title']) ?></a>
                </h3>

                <p class="post-card-summary"><?= e((string) ($post['summary'] ?? '')) ?></p>

                <div class="post-card-footer">
                  <span class="post-author"><?= e((string) ($post['author_name'] ?? 'Decor')) ?></span>
                  <a href="<?= e($detailHref) ?>" class="post-read-more">
                    Đọc tiếp
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                      <line x1="5" y1="12" x2="19" y2="12"/>
                      <polyline points="12 5 19 12 12 19"/>
                    </svg>
                  </a>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
          <nav class="pagination-nav" aria-label="Phân trang bài viết">
            <ul class="pagination-list">
              <li>
                <?php if ($page > 1): ?>
                  <a class="page-btn" href="<?= e($buildPageUrl($page - 1)) ?>" aria-label="Trang trước">&lsaquo;</a>
                <?php else: ?>
                  <span class="page-btn page-btn--disabled" aria-disabled="true">&lsaquo;</span>
                <?php endif; ?>
              </li>
              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li>
                  <a
                    class="page-btn<?= $i === $page ? ' is-active' : '' ?>"
                    href="<?= e($buildPageUrl($i)) ?>"
                    <?= $i === $page ? 'aria-current="page"' : '' ?>
                  ><?= $i ?></a>
                </li>
              <?php endfor; ?>
              <li>
                <?php if ($page < $totalPages): ?>
                  <a class="page-btn" href="<?= e($buildPageUrl($page + 1)) ?>" aria-label="Trang sau">&rsaquo;</a>
                <?php else: ?>
                  <span class="page-btn page-btn--disabled" aria-disabled="true">&rsaquo;</span>
                <?php endif; ?>
              </li>
            </ul>
          </nav>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <aside class="posts-sidebar" aria-label="Bài viết mới nhất">
      <section class="posts-panel sidebar-widget">
        <h2 class="widget-title">Bài viết mới nhất</h2>
        <ul class="mini-post-list">
          <?php foreach ($latestPosts as $lp): ?>
            <?php $lpHref = base_url('/posts/' . rawurlencode((string) $lp['slug'])); ?>
            <li class="mini-post-item">
              <a href="<?= e($lpHref) ?>" class="mini-post-thumb">
                <img src="<?= e($thumbUrl($lp['thumbnail'] ?? '')) ?>" alt="<?= e((string) $lp['title']) ?>" loading="lazy">
              </a>
              <div>
                <a href="<?= e($lpHref) ?>" class="mini-post-title"><?= e((string) $lp['title']) ?></a>
                <span class="mini-post-date">
                  <?= e(date('d/m/Y', strtotime((string) $lp['created_at']))) ?> &bull; <?= (int) $lp['views'] ?> lượt xem
                </span>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </section>
    </aside>
  </div>
</section>
