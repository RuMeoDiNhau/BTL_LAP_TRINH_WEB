<?php
/** ===== #2 About/FAQ ===== Footer riêng, chỉ render khi $useShell = true. */
// Chỉ link route có thật; các mục null hiện chữ, không tự thêm route.
$dcFooterColumns = [
    'Company' => [
        ['Home', '/'],
        ['About', '/about'],
        ['News', null],
    ],
    'Explore' => [
        ['Products', '/products'],
        ['Pricing', null],
        ['Contact', null],
    ],
    'Help' => [
        ['FAQs', '/faq'],
    ],
];
$dcFooterItem = static function (array $item): string {
    $label = (string) $item[0];
    $path = $item[1] === null ? null : (string) $item[1];
    if ($path === null) {
        return '<li><span class="dc-shell-footer__text">' . e($label) . '</span></li>';
    }

    return '<li><a href="' . e(url($path)) . '">' . e($label) . '</a></li>';
};
$dcSocialLabels = [
    'facebook'  => 'Facebook',
    'instagram' => 'Instagram',
    'twitter-x' => 'X (Twitter)',
    'youtube'   => 'YouTube',
    'pinterest' => 'Pinterest',
];
?>
<footer class="dc-shell-footer">
  <div class="dc-shell-footer__inner">
    <div class="dc-shell-footer__grid">
      <div class="dc-shell-footer__brand">
        <a class="dc-shell-footer__logo" href="<?= e(base_url('/')) ?>">Decor</a>
        <ul class="dc-shell-social">
          <?php foreach ($dcSocialLabels as $dcId => $dcLabel): ?>
            <li><a href="#" aria-label="<?= e($dcLabel) ?>"><?= $dcIcon($dcId, 14) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <?php foreach ($dcFooterColumns as $dcTitle => $dcItems): ?>
        <div>
          <h2 class="dc-shell-footer__title"><?= e($dcTitle) ?></h2>
          <ul class="dc-shell-footer__links">
            <?php foreach ($dcItems as $dcItem): ?>
              <?= $dcFooterItem($dcItem) ?>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>

      <div>
        <h2 class="dc-shell-footer__title">Liên hệ</h2>
        <address class="dc-shell-footer__contact">
          <?php if ($dcFooterSettings['address'] !== ''): ?>
            <p><span class="dc-shell-footer__label">Địa chỉ:</span> <?= e($dcFooterSettings['address']) ?></p>
          <?php endif; ?>
          <p><span class="dc-shell-footer__label">Email:</span> <a href="mailto:<?= e($dcFooterSettings['email']) ?>"><?= e($dcFooterSettings['email']) ?></a></p>
          <p><span class="dc-shell-footer__label">Điện thoại:</span> <a href="<?= e($dcPhoneHref) ?>"><?= e($dcFooterSettings['phone']) ?></a></p>
        </address>
      </div>
    </div>

    <div class="dc-shell-footer__bottom">
      <div class="dc-shell-footer__logos">
        <div class="dc-shell-footer__shipping">
          <span class="dc-shell-footer__label">Đơn vị vận chuyển:</span>
          <img src="<?= e(asset('images/arct-icon.png')) ?>" alt="Arctic Shipping" width="38" height="24">
          <img src="<?= e(asset('images/dhl-logo.png')) ?>" alt="DHL" width="60" height="24">
        </div>
        <div class="dc-shell-footer__payments">
          <span class="dc-shell-footer__label">Phương thức thanh toán:</span>
          <img src="<?= e(asset('images/visa-card.png')) ?>" alt="Visa" width="30" height="24">
          <img src="<?= e(asset('images/paypal-card.png')) ?>" alt="PayPal" width="29" height="24">
          <img src="<?= e(asset('images/master-card.png')) ?>" alt="MasterCard" width="30" height="24">
        </div>
      </div>
      <p class="dc-shell-footer__copyright">&copy; Bản quyền 2026 DECOR E-COMMERCE. Bảo lưu mọi quyền.</p>
    </div>
  </div>
</footer>
