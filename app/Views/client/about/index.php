<?php
/**
 * About page: keeps the template DOM/classes, only text comes from the DB.
 * All DB values are plain text and go through e() + nl2br().
 *
 * @var array<string, array<string, mixed>> $sections
 * @var array<int, array<string, mixed>> $latestPosts
 */

$latestPosts = $latestPosts ?? [];

$text = static function (string $key, string $default = '') use ($sections): string {
    $value = $sections[$key]['content'] ?? '';
    return $value === '' ? $default : (string)$value;
};

$heading = static function (string $key, string $default = '') use ($sections): string {
    $value = $sections[$key]['title'] ?? '';
    return $value === '' ? $default : (string)$value;
};

// Story images are fixed local assets, always emitted through asset().
$storyImages = [
    'journey'    => 'assets/images/post-thumbnail-1.jpg',
    'who_we_are' => 'assets/images/post-thumbnail-2.jpg',
];

$featureIcons = ['calendar', 'shopping-bag', 'gift', 'arrow-cycle'];
?>
<section class="page-title page-title--about padding-large mt-5">
    <div class="container" data-aos="fade-up">
        <div class="row">
            <div class="d-flex flex-wrap flex-column justify-content-between align-items-center">
                <h1 class="display-5 fw-bold">About Us</h1>
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <a class="breadcrumb-item" href="<?= e(url('')) ?>">Home</a>
                    <span class="breadcrumb-item active" aria-current="page">About Us</span>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="details">
    <div class="container" data-aos="fade-up">
        <div class="row">
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

<section class="padding-medium">
    <div class="vertical-element" data-aos="fade-up">
        <div class="container">
            <div class="row d-flex align-items-center bg-light">
                <div class="col-md-6">
                    <div class="image-holder">
                        <img src="<?= e(asset($storyImages['journey'])) ?>"
                             class="img-fluid" alt="A living room designed by DECOR">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="section-element p-5">
                        <h2><?= e($heading('journey', 'The beginning of our journey')) ?></h2>
                        <p><?= nl2br(e($text(
                            'journey',
                            'DECOR started with a simple wish: to bring thoughtful decor ideas into every home. We keep learning and listening so that every product and every recommendation suits modern everyday life.'
                        ))) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="vertical-element py-5" data-aos="fade-up">
        <div class="container">
            <div class="row d-flex align-items-center bg-light">
                <div class="col-md-6">
                    <div class="section-element p-5">
                        <h2><?= e($heading('who_we_are', 'Who are we?')) ?></h2>
                        <p><?= nl2br(e($text(
                            'who_we_are',
                            'We are furniture lovers who care about making a room feel warmer and easier to live in. DECOR works with care, openness and respect for each customer’s choices.'
                        ))) ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="image-holder text-right">
                        <img src="<?= e(asset($storyImages['who_we_are'])) ?>"
                             class="img-fluid" alt="A DECOR interior styling corner">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="testimonials" data-aos="fade-up">
    <div class="section-header text-center">
        <h3>Customer Reviews</h3>
    </div>
    <div class="swiper testimonial-swiper overflow-hidden my-5">
        <div class="swiper-wrapper d-flex">
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
                <div class="swiper-slide">
                    <div class="testimonial-item text-center">
                        <blockquote>
                            <p>&ldquo;<?= e($text($key, $quoteDefaults[$index])) ?>&rdquo;</p>
                            <div class="review-title text-uppercase">
                                <?= e($heading($key, $authorDefaults[$index])) ?>
                            </div>
                        </blockquote>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- Swiper.js creates the bullets here at runtime. -->
    <div class="testimonial-swiper-pagination d-flex justify-content-center mb-5"></div>
</section>

<section id="features" class="padding-medium">
    <div class="container-fluid" data-aos="fade-up">
        <div class="row">
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
                if (str_starts_with((string)$key, 'feature_')) {
                    $featureRows[] = [
                        'key'  => (string)$key,
                        'sort' => (int)($row['sort_order'] ?? 0),
                    ];
                }
            }
            usort($featureRows, static function (array $a, array $b): int {
                return ($a['sort'] <=> $b['sort']) ?: strcmp($a['key'], $b['key']);
            });
            ?>
            <?php foreach (array_slice($featureRows, 0, 4) as $index => $row): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="py-5">
                        <svg width="40" height="40" viewBox="0 0 24 24" class="text-dark" aria-hidden="true">
                            <use href="#<?= e($featureIcons[$index] ?? 'arrow-cycle') ?>"></use>
                        </svg>
                        <h4 class="h5 text-capitalize my-1">
                            <?= e($heading($row['key'], $featureDefaults[$index][0])) ?>
                        </h4>
                        <p><?= nl2br(e($text($row['key'], $featureDefaults[$index][1]))) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($latestPosts !== []): ?>
    <section class="blog">
        <div class="container" data-aos="fade-up">
            <div class="d-flex flex-wrap justify-content-between align-items-center mt-5 mb-3">
                <h4>Read Blog Posts</h4>
                <a href="<?= e(url('news')) ?>" class="btn-link">View All</a>
            </div>
            <div class="row">
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
                        <article class="post-item">
                            <div class="post-image">
                                <a href="<?= e($postUrl) ?>">
                                    <img src="<?= e(asset($thumb)) ?>"
                                         alt="<?= e($post['title']) ?>"
                                         class="post-grid-image img-fluid">
                                </a>
                            </div>
                            <div class="post-content d-flex flex-wrap gap-2 my-3">
                                <div class="post-meta text-uppercase text-muted text-secondary">
                                    <span class="post-category"><?= e($post['category_name']) ?> /</span>
                                    <span class="meta-day"><?= e($dateLabel) ?></span>
                                </div>
                                <h3><a href="<?= e($postUrl) ?>"><?= e($post['title']) ?></a></h3>
                                <p><?= e($post['excerpt']) ?></p>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
