<?php
/**
 * Footer tự viết: logo, menu từ config/nav.php, liên hệ từ settings(), social, thanh toán.
 * Không dùng markup/class của template DECOR.
 */
$siteName = setting('site_name', 'DECOR');
$email = setting('email', 'contact@decor.vn');
$phone = setting('phone', '0901234567');
$address = setting('address', '');
$phoneHref = 'tel:' . preg_replace('/[^0-9+]/', '', $phone);

$nav = require dirname(__DIR__, 4) . '/config/nav.php';
$labels = [];
foreach (($nav['items'] ?? []) as $item) {
    if (is_array($item) && !empty($item['enabled'])) {
        $labels[(string)$item['route']] = (string)$item['label'];
    }
}

$footerGroups = [
    'Company' => ['', 'about', 'news'],
    'Explore' => ['products', 'pricing', 'contact'],
    'Help'    => ['faq'],
];
?>
<footer class="dc-footer">
    <div class="container-fluid dc-footer__inner py-5">
        <div class="row g-4">
            <div class="col-12 col-md-4 col-lg-3">
                <a href="<?= e(url('')) ?>">
                    <img class="dc-footer__brand" src="<?= e(asset('assets/images/logo-footer.png')) ?>"
                         alt="<?= e($siteName) ?>">
                </a>
                <ul class="list-unstyled d-flex flex-wrap dc-social mt-4 mb-0">
                    <li><a href="#" aria-label="Facebook"><?= icon('facebook') ?></a></li>
                    <li><a href="#" aria-label="Instagram"><?= icon('instagram') ?></a></li>
                    <li><a href="#" aria-label="X (Twitter)"><?= icon('twitter-x') ?></a></li>
                    <li><a href="#" aria-label="YouTube"><?= icon('youtube') ?></a></li>
                    <li><a href="#" aria-label="Pinterest"><?= icon('pinterest') ?></a></li>
                </ul>
            </div>

            <?php foreach ($footerGroups as $title => $routes): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <h2 class="dc-footer__title"><?= e($title) ?></h2>
                    <ul class="list-unstyled dc-footer__links mb-0">
                        <?php foreach ($routes as $route): ?>
                            <?php if (isset($labels[$route])): ?>
                                <li><a href="<?= e(url($route)) ?>"><?= e($labels[$route]) ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>

            <div class="col-12 col-md-6 col-lg-3">
                <h2 class="dc-footer__title">Contact Us</h2>
                <address class="dc-footer__contact mb-0">
                    <?php if ($address !== ''): ?><p><?= e($address) ?></p><?php endif; ?>
                    <p>Email: <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></p>
                    <p>Phone: <a href="<?= e($phoneHref) ?>"><?= e($phone) ?></a></p>
                </address>
            </div>
        </div>
    </div>

    <div class="dc-footer__bottom">
        <div class="container-fluid dc-footer__inner">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-lg-6">
                    <div class="dc-shipping d-flex flex-wrap align-items-center gap-3">
                        <span>We ship with:</span>
                        <img src="<?= e(asset('assets/images/arct-icon.png')) ?>" alt="Arctic Shipping">
                        <img src="<?= e(asset('assets/images/dhl-logo.png')) ?>" alt="DHL">
                    </div>
                    <div class="dc-payments d-flex flex-wrap align-items-center gap-3 mt-2">
                        <span>Payment option:</span>
                        <img src="<?= e(asset('assets/images/visa-card.png')) ?>" alt="Visa">
                        <img src="<?= e(asset('assets/images/paypal-card.png')) ?>" alt="PayPal">
                        <img src="<?= e(asset('assets/images/master-card.png')) ?>" alt="MasterCard">
                    </div>
                </div>
                <div class="col-12 col-lg-6 text-lg-end">
                    <p class="mb-0">© Copyright <?= date('Y') ?> <?= e($siteName) ?>. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</footer>
