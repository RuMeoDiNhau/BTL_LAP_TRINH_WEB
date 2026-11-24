<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Quản lý thông tin cá nhân, mật khẩu và ảnh đại diện của bạn tại Decor.">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title ?? 'Tài khoản') ?> | Decor</title>
    <link rel="stylesheet" href="<?= e(asset('css/pages/account.css')) ?>">
    <script src="<?= e(asset('js/pages/account.js')) ?>" defer></script>
</head>
<body>
<a class="skip-link" href="#main">Đến nội dung chính</a>
<header class="site-header">
    <a class="brand" href="<?= e(url('/')) ?>" aria-label="Decor — Tài khoản">Decor<span class="brand-dot">.</span></a>
    <span class="header-tagline">KHÔNG GIAN SỐNG CỦA BẠN</span>
    <nav class="header-actions" aria-label="Tài khoản">
        <?php if ($user ?? null): ?>
            <a class="header-user" href="<?= e(url('/account/profile')) ?>">
                <?php if ($avatar = avatar_url($user['avatar'])): ?>
                    <img class="avatar avatar--tiny" src="<?= e($avatar) ?>" alt="" width="32" height="32">
                <?php else: ?>
                    <span class="avatar avatar--tiny" aria-hidden="true"><?= e(initials($user['full_name'])) ?></span>
                <?php endif; ?>
                <span><?= e($user['full_name']) ?></span>
            </a>
            <form action="<?= e(url('/logout')) ?>" method="post">
                <?= csrf_field() ?>
                <button class="text-button" type="submit">Đăng xuất</button>
            </form>
        <?php else: ?>
            <a href="<?= e(url('/login')) ?>">Đăng nhập</a>
            <a class="button button--small" href="<?= e(url('/register')) ?>">Đăng ký</a>
        <?php endif; ?>
    </nav>
</header>
<main id="main" class="site-main">
    <?php if ($notice = take_flash()): ?>
        <div class="notice notice--success" role="status"><?= e($notice) ?></div>
    <?php endif; ?>
    <?php require $viewFile; ?>
</main>
<footer class="site-footer"><span class="footer-brand">Decor.</span><span>Chăm chút không gian. Bắt đầu từ bạn.</span><span>© <?= date('Y') ?> Decor</span></footer>
</body>
</html>
