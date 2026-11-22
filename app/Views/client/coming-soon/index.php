<?php
/**
 * TEMP VIEW - remove when Products/Pricing/News/Contact go live.
 * Markup tự viết, chỉ dùng Bootstrap utilities + class dc-.
 *
 * @var string $pageTitle
 */
$pageTitle = $pageTitle ?? 'Coming Soon';
?>
<section class="dc-page-head">
    <div class="container">
        <h1 class="display-5 fw-bold"><?= e($pageTitle) ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= e(url('')) ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= e($pageTitle) ?></li>
            </ol>
        </nav>
    </div>
</section>

<section class="dc-coming">
    <div class="container">
        <p>This page is being prepared. Please check back soon.</p>
        <a class="btn dc-btn-primary" href="<?= e(url('')) ?>">Back to Home</a>
    </div>
</section>
