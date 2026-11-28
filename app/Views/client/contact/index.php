<?php
declare(strict_types=1);

$address = trim((string) ($settings['address'] ?? ''));
$phone = trim((string) ($settings['phone'] ?? ''));
$email = trim((string) ($settings['email'] ?? ''));
?>
<section class="shop-front shop-shell dc-contact-page" aria-labelledby="contact-title">
  <header class="dc-contact-heading">
    <p class="dc-contact-eyebrow">Hỗ trợ khách hàng</p>
    <h1 id="contact-title">Liên hệ với chúng tôi</h1>
    <p>Gửi câu hỏi hoặc lời nhắn cho Decor. Chúng tôi sẽ phản hồi bạn sớm nhất có thể.</p>
  </header>

  <div class="dc-contact-grid">
    <aside class="dc-contact-info" aria-labelledby="contact-info-title">
      <h2 id="contact-info-title">Thông tin liên hệ</h2>
      <p class="dc-contact-info__intro">Bạn có thể liên hệ với chúng tôi qua các thông tin dưới đây.</p>

      <dl class="dc-contact-details">
        <div>
          <dt>Địa chỉ</dt>
          <dd><?= e($address === '' ? 'Đang cập nhật' : $address) ?></dd>
        </div>
        <div>
          <dt>Điện thoại</dt>
          <dd>
            <?php if ($phone !== ''): ?>
              <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone) ?? '') ?>"><?= e($phone) ?></a>
            <?php else: ?>
              Đang cập nhật
            <?php endif; ?>
          </dd>
        </div>
        <div>
          <dt>Email</dt>
          <dd>
            <?php if ($email !== ''): ?>
              <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
            <?php else: ?>
              Đang cập nhật
            <?php endif; ?>
          </dd>
        </div>
      </dl>
    </aside>

    <div class="dc-contact-form-panel">
      <h2>Gửi lời nhắn</h2>
      <p class="dc-contact-form-panel__intro">Các trường có dấu <span aria-hidden="true">*</span> là bắt buộc.</p>

      <?php if (!empty($errors['_form'])): ?>
        <div class="dc-contact-alert" role="alert"><?= e($errors['_form']) ?></div>
      <?php endif; ?>

      <form action="<?= e(base_url('/contact')) ?>" method="post" class="dc-contact-form">
        <?= csrf_field() ?>

        <div class="dc-contact-form__row">
          <div class="shop-field">
            <label for="contact-name">Họ và tên <span aria-hidden="true">*</span></label>
            <input id="contact-name" name="name" type="text" autocomplete="name" maxlength="100" minlength="2" required value="<?= e($old['name'] ?? '') ?>"<?= isset($errors['name']) ? ' aria-invalid="true" aria-describedby="contact-name-error"' : '' ?>>
            <?php if (isset($errors['name'])): ?><small class="dc-contact-field-error" id="contact-name-error"><?= e($errors['name']) ?></small><?php endif; ?>
          </div>

          <div class="shop-field">
            <label for="contact-email">Email <span aria-hidden="true">*</span></label>
            <input id="contact-email" name="email" type="email" autocomplete="email" maxlength="150" required value="<?= e($old['email'] ?? '') ?>"<?= isset($errors['email']) ? ' aria-invalid="true" aria-describedby="contact-email-error"' : '' ?>>
            <?php if (isset($errors['email'])): ?><small class="dc-contact-field-error" id="contact-email-error"><?= e($errors['email']) ?></small><?php endif; ?>
          </div>
        </div>

        <div class="dc-contact-form__row">
          <div class="shop-field">
            <label for="contact-phone">Số điện thoại</label>
            <input id="contact-phone" name="phone" type="tel" autocomplete="tel" maxlength="20" value="<?= e($old['phone'] ?? '') ?>"<?= isset($errors['phone']) ? ' aria-invalid="true" aria-describedby="contact-phone-error"' : '' ?>>
            <?php if (isset($errors['phone'])): ?><small class="dc-contact-field-error" id="contact-phone-error"><?= e($errors['phone']) ?></small><?php endif; ?>
          </div>

          <div class="shop-field">
            <label for="contact-subject">Chủ đề</label>
            <input id="contact-subject" name="subject" type="text" maxlength="200" value="<?= e($old['subject'] ?? '') ?>"<?= isset($errors['subject']) ? ' aria-invalid="true" aria-describedby="contact-subject-error"' : '' ?>>
            <?php if (isset($errors['subject'])): ?><small class="dc-contact-field-error" id="contact-subject-error"><?= e($errors['subject']) ?></small><?php endif; ?>
          </div>
        </div>

        <div class="shop-field">
          <label for="contact-message">Nội dung <span aria-hidden="true">*</span></label>
          <textarea id="contact-message" name="message" rows="6" maxlength="5000" required<?= isset($errors['message']) ? ' aria-invalid="true" aria-describedby="contact-message-error"' : '' ?>><?= e($old['message'] ?? '') ?></textarea>
          <?php if (isset($errors['message'])): ?><small class="dc-contact-field-error" id="contact-message-error"><?= e($errors['message']) ?></small><?php endif; ?>
        </div>

        <button class="shop-btn dc-contact-submit" type="submit">Gửi liên hệ</button>
      </form>
    </div>
  </div>
</section>
