<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($description) ?>">
    <link rel="icon" type="image/png" href="<?= e(asset('assets/images/logo.png')) ?>">

    <link rel="canonical" href="<?= e($canonicalUrl) ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($description) ?>">
    <meta property="og:url" content="<?= e($canonicalUrl) ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">

    <!-- Chỉ thư viện được phép + CSS tự viết. Không nạp decor/vendor/template JS. -->
    <link rel="stylesheet" href="<?= e(asset('assets/vendor/bootstrap/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/swiper-bundle.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/fonts.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/site.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/about-faq.css')) ?>">
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>

    <main id="main-content" class="dc-main"><?= $content ?></main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="<?= e(asset('assets/vendor/bootstrap/bootstrap.bundle.min.js')) ?>"></script>
    <script src="<?= e(asset('assets/js/swiper-bundle.min.js')) ?>"></script>
    <script src="<?= e(asset('assets/js/faq-form.js')) ?>"></script>
    <script src="<?= e(asset('assets/js/main.js')) ?>"></script>
</body>
</html>
