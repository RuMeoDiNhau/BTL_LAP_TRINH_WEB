<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($description) ?>">

    <link rel="canonical" href="<?= e($canonicalUrl) ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($description) ?>">
    <meta property="og:url" content="<?= e($canonicalUrl) ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">

    <!-- Local assets in the same order as the template; fonts.css replaces CDN fonts. -->
    <link rel="stylesheet" href="<?= e(asset('assets/css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/vendor.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/swiper-bundle.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/fonts.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/decor.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/about-faq.css')) ?>">
</head>
<body>
    <?php include __DIR__ . '/partials/svg-sprite.php'; ?>
    <?php include __DIR__ . '/partials/header.php'; ?>

    <main id="main-content"><?= $content ?></main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <!-- Script order follows the template; faq-form.js only runs on the FAQ page. -->
    <script src="<?= e(asset('assets/js/bootstrap.bundle.min.js')) ?>"></script>
    <script src="<?= e(asset('assets/js/swiper-bundle.min.js')) ?>"></script>
    <script src="<?= e(asset('assets/js/SmoothScroll.js')) ?>"></script>
    <script src="<?= e(asset('assets/js/jquery.min.js')) ?>"></script>
    <script src="<?= e(asset('assets/js/plugins.js')) ?>"></script>
    <script src="<?= e(asset('assets/js/script.js')) ?>"></script>
    <script src="<?= e(asset('assets/js/faq-form.js')) ?>"></script>
</body>
</html>
