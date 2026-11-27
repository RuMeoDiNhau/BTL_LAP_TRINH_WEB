<?php
/**
 * About page: nội dung đọc từ page_contents (DB). Không hardcode chữ nội dung.
 *
 * @var array<string, array<string, mixed>> $sections
 * @var array<int, array<string, mixed>> $latestPosts
 */
$latestPosts = $latestPosts ?? [];

$text = static function (string $key) use ($sections): string {
    return trim((string)($sections[$key]['content'] ?? ''));
};
$heading = static function (string $key) use ($sections): string {
    return trim((string)($sections[$key]['title'] ?? ''));
};
$img = static function (string $key) use ($sections): string {
    return trim((string)($sections[$key]['image'] ?? ''));
};

$heroTitle = $heading('hero');
$introKeys = ['intro_1', 'intro_2', 'intro_3'];
$storyKeys = ['journey', 'who_we_are'];
$testimonialKeys = ['testimonial_1', 'testimonial_2', 'testimonial_3', 'testimonial_4'];
$featureIcons = ['calendar-event', 'bag', 'gift', 'arrow-repeat'];
?>
<section class="dc-page-head">
    <div class="container">
        <h1 class="display-5 fw-bold"><?= e($heroTitle) ?></h1>
    </div>
</section>

<section class="dc-section dc-intro">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($introKeys as $key): ?>
                <?php $content = $text($key); if ($content === '') continue; ?>
                <div class="col-md-4">
                    <p><?= nl2br(e($content)) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="dc-section dc-story">
    <?php foreach ($storyKeys as $index => $key): ?>
        <?php
        $title = $heading($key);
        $content = $text($key);
        $image = $img($key);
        if ($title === '' && $content === '' && $image === '') continue;
        $reverse = $index % 2 === 1 ? ' flex-md-row-reverse' : '';
        ?>
        <div class="row g-0 dc-surface dc-story__row<?= $reverse ?>">
            <?php if ($image !== ''): ?>
                <div class="col-md-6">
                    <img class="dc-story__img" src="<?= e(asset($image)) ?>" alt="Ảnh minh họa: <?= e($title) ?>">
                </div>
            <?php endif; ?>
            <div class="<?= $image !== '' ? 'col-md-6' : 'col-12' ?> d-flex align-items-center p-4 p-lg-5">
                <div>
                    <?php if ($title !== ''): ?><h2><?= e($title) ?></h2><?php endif; ?>
                    <?php if ($content !== ''): ?><p><?= nl2br(e($content)) ?></p><?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</section>

<?php
$testimonials = [];
foreach ($testimonialKeys as $key) {
    $quote = $text($key);
    $author = $heading($key);
    if ($quote !== '' || $author !== '') {
        $testimonials[] = ['quote' => $quote, 'author' => $author];
    }
}
?>
<?php if ($testimonials !== []): ?>
<section class="dc-section dc-testimonials">
    <div class="container">
        <div class="swiper dc-testimonial-swiper" role="region" aria-label="Chứng thực của khách hàng">
            <div class="swiper-wrapper">
                <?php foreach ($testimonials as $item): ?>
                    <div class="swiper-slide d-flex">
                        <blockquote class="dc-quote w-100">
                            <?php if ($item['quote'] !== ''): ?><p>&ldquo;<?= e($item['quote']) ?>&rdquo;</p><?php endif; ?>
                            <?php if ($item['author'] !== ''): ?><footer class="dc-quote__author"><?= e($item['author']) ?></footer><?php endif; ?>
                        </blockquote>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="swiper-pagination dc-testimonial-pagination"></div>
    </div>
</section>
<?php endif; ?>

<?php
$featureRows = [];
for ($i = 1; $i <= 4; $i++) {
    $key = 'feature_' . $i;
    $title = $heading($key);
    $content = $text($key);
    if ($title !== '' || $content !== '') {
        $featureRows[] = ['title' => $title, 'content' => $content, 'icon' => $featureIcons[$i - 1] ?? 'arrow-repeat'];
    }
}
?>
<?php if ($featureRows !== []): ?>
<section class="dc-section dc-features">
    <div class="container-fluid">
        <div class="row g-4">
            <?php foreach ($featureRows as $row): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="dc-feature">
                        <svg class="bi dc-feature__icon" aria-hidden="true" width="1em" height="1em" fill="currentColor">
                            <use href="<?= e(asset('assets/icons/bootstrap-icons.svg') . '#' . $row['icon']) ?>"></use>
                        </svg>
                        <?php if ($row['title'] !== ''): ?><h2 class="h5 text-capitalize"><?= e($row['title']) ?></h2><?php endif; ?>
                        <?php if ($row['content'] !== ''): ?><p><?= nl2br(e($row['content'])) ?></p><?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($latestPosts !== []): ?>
    <section class="dc-section dc-posts">
        <div class="container">
            <div class="row g-4">
                <?php foreach ($latestPosts as $post): ?>
                    <?php
                    $postUrl = url('posts/' . $post['slug']);
                    $thumb = trim((string)($post['thumbnail'] ?? ''));
                    $timestamp = strtotime((string)($post['published_at'] ?? ''));
                    // ===== #2 About/FAQ: ngày tháng theo tiếng Việt (dd/mm/yyyy).
                    $dateLabel = $timestamp === false ? '' : date('d/m/Y', $timestamp);
                    ?>
                    <div class="col-md-4">
                        <article class="dc-post">
                            <?php if ($thumb !== ''): ?>
                                <a href="<?= e($postUrl) ?>">
                                    <img class="img-fluid" src="<?= e(asset(ltrim($thumb, '/'))) ?>" alt="Ảnh bài viết: <?= e($post['title']) ?>" loading="lazy">
                                </a>
                            <?php endif; ?>
                            <div class="mt-3">
                                <p class="dc-post__meta mb-1">
                                    <span><?= e($post['category_name']) ?></span><?php if ($dateLabel !== ''): ?> / <span><?= e($dateLabel) ?></span><?php endif; ?>
                                </p>
                                <h3 class="dc-post__title">
                                    <a href="<?= e($postUrl) ?>"><?= e($post['title']) ?></a>
                                </h3>
                                <?php if ((string)$post['excerpt'] !== ''): ?><p><?= e($post['excerpt']) ?></p><?php endif; ?>
                                <a class="dc-post__more" href="<?= e($postUrl) ?>">Đọc thêm</a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
