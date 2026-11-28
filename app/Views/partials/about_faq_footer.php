<?php
$dcFooterColumns = [
    'Về Decor' => [
        ['Trang chủ', '/'],
        ['Giới thiệu', '/about'],
        ['Tin tức', '/posts'],
    ],
    'Mua sắm' => [
        ['Sản phẩm', '/products'],
        ['Giỏ hàng', '/cart'],
    ],
    'Hỗ trợ' => [
        ['Câu hỏi thường gặp', '/faq'],
        ['Liên hệ', '/contact'],
    ],
];
?>
<footer class="dc-shell-footer">
  <div class="dc-shell-footer__inner">
    <div class="dc-shell-footer__grid">
      <div class="dc-shell-footer__brand">
        <a class="dc-shell-footer__logo" href="<?= e(base_url('/')) ?>" aria-label="Decor – Trang chủ">
          <?php if (!empty($brandLogo)): ?>
            <img src="<?= e($brandLogo) ?>" alt="" width="150" height="60">
          <?php else: ?>
            Decor
          <?php endif; ?>
        </a>
      </div>

      <?php foreach ($dcFooterColumns as $dcTitle => $dcItems): ?>
        <div>
          <h2 class="dc-shell-footer__title"><?= e($dcTitle) ?></h2>
          <ul class="dc-shell-footer__links">
            <?php foreach ($dcItems as [$dcLabel, $dcPath]): ?>
              <li><a href="<?= e(base_url($dcPath)) ?>"><?= e($dcLabel) ?></a></li>
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
      <p class="dc-shell-footer__copyright">&copy; Bản quyền 2026 DECOR E-COMMERCE. Bảo lưu mọi quyền.</p>
    </div>
  </div>
</footer>
