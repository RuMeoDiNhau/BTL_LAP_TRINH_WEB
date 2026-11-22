<?php
/**
 * About page tự viết: chỉ Bootstrap utilities + class dc-, không dùng template DECOR.
 * Nội dung vẫn lấy từ page_contents; mọi giá trị đều qua e().
 *
 * @var array<string, array<string, mixed>> $sections
 * @var array<int, array<string, mixed>> $latestPosts
 */
$latestPosts = $latestPosts ?? [];

$text = function (string $key, string $default = '') use ($sections): string {
    $value = $sections[$key]['content'] ?? '';
    return $value === '' ? $default : (string)$value;
};

$heading = function (string $key, string $default = '') use ($sections): string {
    $value = $sections[$key]['title'] ?? '';
    return $value === '' ? $default : (string)$value;
};

$storyImages = [
    'journey'    => 'assets/images/post-thumbnail-1.jpg',
    'who_we_are' => 'assets/images/post-thumbnail-2.jpg',
];
$featureIcons = ['calendar-event', 'bag', 'gift', 'arrow-repeat'];
?>
<section class="dc-page-head">
    <div class="container">
        <h1 class="display-5 fw-bold">About Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= e(url('')) ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">About Us</li>
            </ol>
        </nav>
    </div>
</section>

<section class="dc-section dc-intro">
    <div class="container">
        <div class="row g-4">
            <?php
            $introDefaults = [
                'DECOR is a destination for anyone who loves beautiful, comfortable living spaces.',
                'We believe that every small detail helps make a home feel more relaxed and welcoming.',
                'Our team listens first and stays with you from the initial idea to the final detail.',
            ];
            foreach (['intro_1', 'intro_2', 'intro_3'] as $index => $key):
            ?>
                <div class="col-md-4">
                    <p><?= nl2br(e($text($key, $introDefaults[$index]))) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="dc-section dc-story">
    <div class="row g-0 dc-surface dc-story__row">
        <div class="col-md-6">
            <img class="dc-story__img" src="<?= e(asset($storyImages['journey'])) ?>"
                 alt="A living room designed by DECOR">
        </div>
        <div class="col-md-6 d-flex align-items-center p-4 p-lg-5">
            <div>
                <h2><?= e($heading('journey', 'The beginning of our journey')) ?></h2>
                <p><?= nl2br(e($text(
                    'journey',
                    'DECOR started with a simple wish: to bring thoughtful decor ideas into every home. We keep learning and listening so that every product and every recommendation suits modern everyday life.'
                ))) ?></p>
            </div>
        </div>
    </div>

    <div class="row g-0 dc-surface dc-story__row flex-md-row-reverse">
        <div class="col-md-6">
            <img class="dc-story__img" src="<?= e(asset($storyImages['who_we_are'])) ?>"
                 alt="A DECOR interior styling corner">
        </div>
        <div class="col-md-6 d-flex align-items-center p-4 p-lg-5">
            <div>
                <h2><?= e($heading('who_we_are', 'Who are we?')) ?></h2>
                <p><?= nl2br(e($text(
                    'who_we_are',
                    'We are furniture lovers who care about making a room feel warmer and easier to live in. DECOR works with care, openness and respect for each customer’s choices.'
                ))) ?></p>
            </div>
        </div>
    </div>
</section>

<section class="dc-section dc-testimonials">
    <div class="container">
        <h2 class="h3 text-center mb-5">Customer Reviews</h2>
        <div class="swiper dc-testimonial-swiper" role="region" aria-label="Customer reviews">
            <div class="swiper-wrapper">
                <?php
                $quoteDefaults = [
                    'The advice was gentle and easy to follow; I felt heard from start to finish.',
                    'Small but thoughtful suggestions made my living room feel much tidier.',
                    'I like how DECOR pays attention to materials and the way things feel every day.',
                    'The support after my purchase made me feel confident about shopping with DECOR.',
                ];
                $authorDefaults = [
                    'DECOR customer',
                    'DECOR partner',
                    'Home decor enthusiast',
                    'DECOR community member',
                ];
                foreach (['testimonial_1', 'testimonial_2', 'testimonial_3', 'testimonial_4'] as $index => $key):
                ?>
                    <div class="swiper-slide d-flex">
                        <blockquote class="dc-quote w-100">
                            <p>&ldquo;<?= e($text($key, $quoteDefaults[$index])) ?>&rdquo;</p>
                            <footer class="dc-quote__author">
                                <?= e($heading($key, $authorDefaults[$index])) ?>
                            </footer>
                        </blockquote>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- Swiper.js tạo bullet vào đây khi chạy. -->
        <div class="swiper-pagination dc-testimonial-pagination"></div>
    </div>
</section>

<section class="dc-section dc-features">
    <div class="container-fluid">
        <div class="row g-4">
            <?php
            $featureDefaults = [
                ['Book An Appointment', 'Book a consultation so we can help you in more detail.'],
                ['Pick up in store', 'Choose your pieces and collect them directly from our store.'],
                ['Special packaging', 'Every gift is wrapped with care and attention.'],
                ['Free global returns', 'Returns are easy under our standard policy.'],
            ];

            // Thứ tự icon đi theo sort_order lưu trong page_contents.
            $featureRows = [];
            foreach ($sections as $key => $row) {
                if (strpos((string)$key, 'feature_') === 0) {
                    $featureRows[] = [
                        'key'  => (string)$key,
                        'sort' => (int)($row['sort_order'] ?? 0),
                    ];
                }
            }
            usort($featureRows, function (array $a, array $b): int {
                return ($a['sort'] <=> $b['sort']) ?: strcmp($a['key'], $b['key']);
            });
            ?>
            <?php foreach (array_slice($featureRows, 0, 4) as $index => $row): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="dc-feature">
                        <?= icon($featureIcons[$index] ?? 'arrow-repeat', 'dc-feature__icon') ?>
                        <h2 class="h5 text-capitalize"><?= e($heading($row['key'], $featureDefaults[$index][0])) ?></h2>
                        <p><?= nl2br(e($text($row['key'], $featureDefaults[$index][1]))) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($latestPosts !== []): ?>
    <section class="dc-section dc-posts">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <h2 class="h4 mb-0">Read Blog Posts</h2>
                <a class="btn btn-link" href="<?= e(url('news')) ?>">View All</a>
            </div>
            <div class="row g-4">
                <?php foreach ($latestPosts as $index => $post): ?>
                    <?php
                    $postUrl = url('news/' . $post['slug']);
                    $thumb = trim((string)($post['thumbnail'] ?? ''));
                    $thumb = $thumb === ''
                        ? 'assets/images/post-thumbnail-' . min(3, $index + 1) . '.jpg'
                        : ltrim($thumb, '/');
                    $timestamp = strtotime((string)($post['published_at'] ?? ''));
                    $dateLabel = $timestamp === false ? '' : date('M d, Y', $timestamp);
                    ?>
                    <div class="col-md-4">
                        <article class="dc-post">
                            <a href="<?= e($postUrl) ?>">
                                <img class="img-fluid" src="<?= e(asset($thumb)) ?>"
                                     alt="<?= e($post['title']) ?>" loading="lazy">
                            </a>
                            <div class="mt-3">
                                <p class="dc-post__meta mb-1">
                                    <span><?= e($post['category_name']) ?></span> /
                                    <span><?= e($dateLabel) ?></span>
                                </p>
                                <h3 class="dc-post__title">
                                    <a href="<?= e($postUrl) ?>"><?= e($post['title']) ?></a>
                                </h3>
                                <p><?= e($post['excerpt']) ?></p>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
