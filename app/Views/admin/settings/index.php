<?php
declare(strict_types=1);

$sectionLabels = ['hero' => 'Nội dung nổi bật', 'welcome' => 'Giới thiệu', 'about' => 'Về Decor'];
$sectionDefaults = [
    'hero' => ['title' => 'Room Decors', 'content' => 'Cozy Furnitures for your personal spaces'],
    'welcome' => ['title' => 'Welcome', 'content' => ''],
    'about' => ['title' => '', 'content' => ''],
];
$showError = static function (string $key) use ($errors): void {
    if (isset($errors[$key])) {
        echo '<small class="ta-field-error">' . e($errors[$key]) . '</small>';
    }
};
?>
<div class="ta-page-header">
  <div>
    <h1 class="ta-page-title">Nội dung trang</h1>
    <p class="ta-page-subtitle">Chỉnh nội dung trang chủ và thông tin liên hệ hiển thị trên website.</p>
  </div>
</div>

<form action="<?= e(base_url('/admin/settings')) ?>" method="post" enctype="multipart/form-data" class="ta-site-content-form">
  <?= csrf_field() ?>
  <?php foreach ($sectionLabels as $key => $label): $section = array_merge($sectionDefaults[$key], $sections[$key] ?? []); ?>
  <section class="ta-card">
    <div class="ta-card-header"><h2 class="ta-card-title"><?= e($label) ?></h2></div>
    <div class="ta-site-form-body">
      <div class="ta-site-field">
        <label for="<?= e($key) ?>-title">Tiêu đề</label>
        <input id="<?= e($key) ?>-title" name="<?= e($key) ?>_title" maxlength="255" value="<?= e($section['title'] ?? '') ?>">
        <?php $showError($key . '_title'); ?>
      </div>
      <div class="ta-site-field">
        <label for="<?= e($key) ?>-content">Nội dung</label>
        <textarea id="<?= e($key) ?>-content" name="<?= e($key) ?>_content" rows="4" maxlength="5000"><?= e($section['content'] ?? '') ?></textarea>
        <?php $showError($key . '_content'); ?>
      </div>
      <div class="ta-site-field">
        <label for="<?= e($key) ?>-image">Ảnh</label>
        <input id="<?= e($key) ?>-image" name="<?= e($key) ?>_image" type="file" accept="image/jpeg,image/png,image/webp">
        <small>JPG, PNG hoặc WebP, tối đa 4 MB.</small>
        <?php $showError($key . '_image'); ?>
        <?php if (!empty($section['image'])): ?><img class="ta-site-image-preview" src="<?= e(base_url('/' . ltrim((string) $section['image'], '/'))) ?>" alt="Ảnh hiện tại của <?= e($label) ?>"><?php endif; ?>
      </div>
    </div>
  </section>
  <?php endforeach; ?>

  <section class="ta-card">
    <div class="ta-card-header"><h2 class="ta-card-title">Thông tin liên hệ</h2></div>
    <div class="ta-site-form-body ta-site-form-grid">
      <?php foreach (['site_name' => ['Tên website', 100], 'phone' => ['Số điện thoại', 20], 'email' => ['Email', 150], 'address' => ['Địa chỉ', 255]] as $key => [$label, $maxLength]): ?>
      <div class="ta-site-field">
        <label for="setting-<?= e($key) ?>"><?= e($label) ?></label>
        <input id="setting-<?= e($key) ?>" name="<?= e($key) ?>" maxlength="<?= (int) $maxLength ?>" value="<?= e((string) ($settings[$key] ?? '')) ?>" <?= $key === 'email' ? 'type="email"' : 'type="text"' ?>>
        <?php $showError($key); ?>
      </div>
      <?php endforeach; ?>
      <div class="ta-site-field">
        <label for="setting-logo">Logo</label>
        <input id="setting-logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp">
        <small>JPG, PNG hoặc WebP, tối đa 4 MB.</small>
        <?php $showError('logo'); ?>
        <?php if (!empty($settings['logo'])): ?><img class="ta-site-image-preview ta-site-logo-preview" src="<?= e(base_url('/' . ltrim((string) $settings['logo'], '/'))) ?>" alt="Logo hiện tại"><?php endif; ?>
      </div>
    </div>
  </section>

  <div class="ta-site-form-actions"><button type="submit" class="ta-btn ta-btn--primary">Lưu thay đổi</button></div>
</form>
