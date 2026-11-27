<?php
$thumbUrl = static function (?string $path): string {
    $p = ltrim((string) $path, '/');
    if ($p === '') {
        return asset('images/posts/post-1.svg');
    }
    return base_url('/' . $p);
};

$postHref = base_url('/posts/' . rawurlencode((string) $post['slug']));
$commentAction = base_url('/posts/' . rawurlencode((string) $post['slug']) . '/comments');
$user = current_user();
?>
<div class="blog-section">
  <div class="container">
    <div class="blog-layout">
      <div>
        <article class="article-card">
          <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= e(base_url('/')) ?>">Trang chủ</a>
            <span aria-hidden="true">/</span>
            <a href="<?= e(base_url('/posts')) ?>">Tin tức</a>
            <span aria-hidden="true">/</span>
            <span class="breadcrumb-current" aria-current="page"><?= e((string) $post['title']) ?></span>
          </nav>

          <h1 class="article-title"><?= e((string) $post['title']) ?></h1>

          <div class="post-meta-row">
            <span class="post-meta-item">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
              <span><?= e((string) ($post['author_name'] ?? 'Decor')) ?></span>
            </span>
            <span class="post-meta-item">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
              <time datetime="<?= e(date('Y-m-d', strtotime((string) $post['created_at']))) ?>">
                <?= e(date('d/m/Y', strtotime((string) $post['created_at']))) ?>
              </time>
            </span>
            <span class="post-meta-item">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              <span><?= (int) $post['views'] ?> lượt xem</span>
            </span>
          </div>

          <img
            class="article-hero-image"
            src="<?= e($thumbUrl($post['thumbnail'] ?? '')) ?>"
            alt="<?= e((string) $post['title']) ?>"
          >

          <div class="article-prose">
            <?= $post['content'] ?>
          </div>
        </article>

        <section id="comments" class="comments-card" aria-label="Bình luận bài viết">
          <div class="comments-header">
            <h2 class="section-heading" style="margin-bottom:0;">
              Bình luận (<?= (int) $totalComments ?>)
            </h2>
          </div>

          <?php if (empty($comments)): ?>
            <p style="color:#475569;margin-bottom:24px;">Chưa có bình luận nào cho bài viết này. Hãy là người đầu tiên chia sẻ cảm nhận!</p>
          <?php else: ?>
            <div class="comment-list">
              <?php foreach ($comments as $c): ?>
                <?php
                $cName = (string) ($c['author_name'] ?? 'Thành viên');
                $parts = preg_split('/\s+/u', trim($cName)) ?: ['TV'];
                $initials = count($parts) >= 2
                    ? mb_strtoupper(mb_substr($parts[0], 0, 1, 'UTF-8') . mb_substr($parts[count($parts) - 1], 0, 1, 'UTF-8'), 'UTF-8')
                    : mb_strtoupper(mb_substr($parts[0], 0, 2, 'UTF-8'), 'UTF-8');
                ?>
                <article class="comment-item">
                  <span class="comment-avatar" aria-hidden="true"><?= e($initials) ?></span>
                  <div>
                    <div class="comment-meta">
                      <span class="comment-author"><?= e($cName) ?></span>
                      <span class="comment-date"><?= e(date('d/m/Y H:i', strtotime((string) $c['created_at']))) ?></span>
                    </div>
                    <p class="comment-body"><?= nl2br(e((string) $c['content'])) ?></p>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>

            <?php if ($totalCommentPages > 1): ?>
              <nav class="pagination-nav" style="margin-top:0;margin-bottom:24px;" aria-label="Phân trang bình luận">
                <ul class="pagination-list">
                  <?php for ($i = 1; $i <= $totalCommentPages; $i++): ?>
                    <li>
                      <a
                        class="page-btn<?= $i === $cpage ? ' is-active' : '' ?>"
                        href="<?= e($postHref . '?cpage=' . $i . '#comments') ?>"
                        <?= $i === $cpage ? 'aria-current="page"' : '' ?>
                      ><?= $i ?></a>
                    </li>
                  <?php endfor; ?>
                </ul>
              </nav>
            <?php endif; ?>
          <?php endif; ?>

          <?php if ($user === null): ?>
            <div class="guest-prompt">
              <div>
                <strong>Bạn cần đăng nhập để gửi bình luận</strong>
                <p style="font-size:13.5px;color:#475569;margin-top:2px;">Chỉ tài khoản Thành viên hoặc Quản trị viên mới có quyền bình luận bài viết.</p>
              </div>
              <a href="<?= e(base_url('/login')) ?>" class="btn btn--primary btn--sm">Đăng nhập ngay</a>
            </div>
          <?php else: ?>
            <div id="commentErrorSummary" class="error-summary" role="alert" tabindex="-1" aria-labelledby="commentErrorTitle">
              <div id="commentErrorTitle" class="error-summary-title">Vui lòng kiểm tra lại bình luận của bạn</div>
              <ul id="commentErrorList"></ul>
            </div>

            <form id="commentForm" action="<?= e($commentAction) ?>" method="post" novalidate>
              <?= csrf_field() ?>
              <div class="comment-form-group">
                <label for="commentInput" class="comment-label">
                  Viết bình luận với tên <strong><?= e((string) ($user['full_name'] ?? 'Thành viên')) ?></strong>
                </label>
                <textarea
                  id="commentInput"
                  name="content"
                  class="comment-textarea"
                  maxlength="1000"
                  aria-describedby="commentInputHint commentInputError"
                  placeholder="Chia sẻ cảm nhận hoặc câu hỏi của bạn về bài viết này (từ 5 đến 1000 ký tự)..."
                  required
                ><?= e($oldComment ?? '') ?></textarea>
                <div class="comment-form-meta">
                  <span id="commentInputHint">Tối thiểu 5 ký tự, tối đa 1000 ký tự.</span>
                  <span id="commentCharCount" aria-live="polite"><?= mb_strlen((string) ($oldComment ?? ''), 'UTF-8') ?> / 1000</span>
                </div>
                <p id="commentInputError" class="field-error-text"></p>
              </div>

              <button type="submit" class="btn btn--primary">Gửi bình luận</button>
            </form>
          <?php endif; ?>
        </section>
      </div>

      <aside class="blog-sidebar" aria-label="Tìm kiếm và bài viết mới">
        <section class="sidebar-widget">
          <h2 class="widget-title">Tìm kiếm bài viết</h2>
          <form action="<?= e(base_url('/posts')) ?>" method="get" role="search">
            <div class="search-input-wrap" style="margin-bottom:10px;">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
              </svg>
              <label for="sidebarSearchInput" class="sr-only">Nhập từ khóa tìm kiếm</label>
              <input type="search" id="sidebarSearchInput" name="keyword" class="search-input" placeholder="Nhập từ khóa...">
            </div>
            <button type="submit" class="btn btn--primary" style="width:100%;">Tìm kiếm</button>
          </form>
        </section>

        <section class="sidebar-widget">
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
                  <span class="mini-post-date"><?= e(date('d/m/Y', strtotime((string) $lp['created_at']))) ?></span>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </section>
      </aside>
    </div>
  </div>
</div>
