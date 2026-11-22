<?php
/**
 * Footer theo DOM mẫu: logo/social -> 3 nhóm liên kết -> contact -> shipping/payment.
 * Mọi thông tin liên hệ lấy từ bảng settings (có mặc định khi CSDL chưa có).
 */
$siteName = setting('site_name', 'DECOR');
$email = setting('email', 'contact@decor.vn');
$phone = setting('phone', '0901234567');
$address = setting('address', '');
$phoneHref = 'tel:' . preg_replace('/[^0-9+]/', '', $phone);
?>
<footer id="footer">
    <div class="container-fluid padding-medium">
        <div class="row d-flex flex-wrap justify-content-between py-5">
            <div class="col-md-3 col-sm-6">
                <div class="footer-menu footer-menu-001">
                    <div class="footer-intro mb-4">
                        <a href="<?= e(url('about')) ?>">
                            <img src="<?= e(asset('assets/images/logo-footer.png')) ?>"
                                 alt="<?= e($siteName) ?>" class="img-fluid">
                        </a>
                    </div>
                    <div class="social-links">
                        <ul class="list-unstyled d-flex flex-wrap gap-4">
                            <li><a href="#" class="text-secondary" aria-label="Facebook"><svg width="24" height="24" viewBox="0 0 24 24"><use href="#facebook"></use></svg></a></li>
                            <li><a href="#" class="text-secondary" aria-label="Twitter"><svg width="24" height="24" viewBox="0 0 24 24"><use href="#twitter"></use></svg></a></li>
                            <li><a href="#" class="text-secondary" aria-label="YouTube"><svg width="24" height="24" viewBox="0 0 24 24"><use href="#youtube"></use></svg></a></li>
                            <li><a href="#" class="text-secondary" aria-label="Pinterest"><svg width="24" height="24" viewBox="0 0 24 24"><use href="#pinterest"></use></svg></a></li>
                            <li><a href="#" class="text-secondary" aria-label="Instagram"><svg width="24" height="24" viewBox="0 0 24 24"><use href="#instagram"></use></svg></a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="footer-menu footer-menu-002">
                    <h4 class="h5 widget-title mb-4">Quick Links</h4>
                    <ul class="menu-list list-unstyled fs-6">
                        <li class="menu-item"><a href="#" class="item-anchor">Home</a></li>
                        <li class="menu-item"><a href="<?= e(url('about')) ?>" class="item-anchor">About</a></li>
                        <li class="menu-item"><a href="#" class="item-anchor">Services</a></li>
                        <li class="menu-item"><a href="#" class="item-anchor">Single item</a></li>
                        <li class="menu-item"><a href="#" class="item-anchor">Contact</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="footer-menu footer-menu-002">
                    <h4 class="h5 widget-title mb-4">About</h4>
                    <ul class="menu-list list-unstyled fs-6">
                        <li class="menu-item"><a href="#" class="item-anchor">How it works</a></li>
                        <li class="menu-item"><a href="<?= e(url('about')) ?>" class="item-anchor">About</a></li>
                        <li class="menu-item"><a href="#" class="item-anchor">Our packages</a></li>
                        <li class="menu-item"><a href="#" class="item-anchor">Promotions</a></li>
                        <li class="menu-item"><a href="#" class="item-anchor">Refer a friend</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="footer-menu footer-menu-003">
                    <h4 class="h5 widget-title mb-4">Help &amp; Info</h4>
                    <ul class="menu-list list-unstyled fs-6">
                        <li class="menu-item"><a href="#" class="item-anchor">Track Your Order</a></li>
                        <li class="menu-item"><a href="#" class="item-anchor">Returns + Exchanges</a></li>
                        <li class="menu-item"><a href="#" class="item-anchor">Shipping + Delivery</a></li>
                        <li class="menu-item"><a href="#" class="item-anchor">Contact Us</a></li>
                        <li class="menu-item"><a href="#" class="item-anchor">Find us easy</a></li>
                        <li class="menu-item"><a href="<?= e(url('faq')) ?>" class="item-anchor">FAQs</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="footer-menu footer-menu-004 border-animation-left">
                    <h4 class="h5 widget-title mb-4">Contact Us</h4>
                    <p><?= e($address) ?></p>
                    <p>Email: <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></p>
                    <p>Phone: <a href="<?= e($phoneHref) ?>"><?= e($phone) ?></a></p>
                </div>
            </div>
        </div>
    </div>
    <div class="border-top py-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 d-flex flex-wrap">
                    <div class="shipping">
                        <span>We ship with:</span>
                        <img src="<?= e(asset('assets/images/arct-icon.png')) ?>" alt="Arctic Shipping">
                        <img src="<?= e(asset('assets/images/dhl-logo.png')) ?>" alt="DHL">
                    </div>
                    <div class="payment-option">
                        <span>Payment Option:</span>
                        <img src="<?= e(asset('assets/images/visa-card.png')) ?>" alt="Visa">
                        <img src="<?= e(asset('assets/images/paypal-card.png')) ?>" alt="PayPal">
                        <img src="<?= e(asset('assets/images/master-card.png')) ?>" alt="MasterCard">
                    </div>
                </div>
                <div class="col-md-6 text-start text-md-end">
                    <p>© Copyright <?= date('Y') ?> <?= e($siteName) ?>. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</footer>
