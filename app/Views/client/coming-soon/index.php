<?php
/**
 * TEMP VIEW - remove this file when Products/Pricing/News/Contact go live.
 * Used by PageController@comingSoon for routes that have no real page yet.
 * @var string $pageTitle
 */
$pageTitle = $pageTitle ?? 'Coming Soon';
?>
<section class="page-title padding-large mt-5">
    <div class="container" data-aos="fade-up">
        <div class="row">
            <div class="d-flex flex-wrap flex-column justify-content-between align-items-center">
                <h1 class="display-5 fw-bold"><?= e($pageTitle) ?></h1>
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <a class="breadcrumb-item" href="<?= e(url('')) ?>">Home</a>
                    <span class="breadcrumb-item active" aria-current="page"><?= e($pageTitle) ?></span>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="details">
    <div class="container" data-aos="fade-up">
        <div class="row">
            <div class="col-12 text-center py-5">
                <p>This page is being prepared. Please check back soon.</p>
                <a class="btn btn-primary btn-lg text-uppercase btn-rounded-none" href="<?= e(url('')) ?>">
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</section>
