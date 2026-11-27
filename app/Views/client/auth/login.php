<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>
<span class="auth-eyebrow">Tài khoản Decor</span>
<h2 class="auth-heading">Chào mừng trở lại</h2>
<p class="auth-subheading">Đăng nhập để tiếp tục khám phá không gian nội thất Decor.</p>

<?php if (!empty($flashSuccess)): ?>
  <div class="auth-alert auth-alert--success is-visible" role="status">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
      <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    <div><?= e($flashSuccess) ?></div>
  </div>
<?php endif; ?>

<div id="loginAlert" class="auth-alert auth-alert--error<?= !empty($flashError) ? ' is-visible' : '' ?>" role="alert" tabindex="-1">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
    <circle cx="12" cy="12" r="10"/>
    <line x1="12" y1="8" x2="12" y2="12"/>
    <line x1="12" y1="16" x2="12.01" y2="16"/>
  </svg>
  <div data-alert-text><?= e($flashError ?? '') ?></div>
</div>

<form id="loginForm" class="auth-form" action="<?= e(base_url('/login')) ?>" method="post" novalidate>
  <?= csrf_field() ?>

  <div class="form-group">
    <label for="loginEmail" class="form-label">Địa chỉ email</label>
    <div class="input-wrapper">
      <input
        type="email"
        id="loginEmail"
        name="email"
        class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
        value="<?= e($old['email'] ?? '') ?>"
        placeholder="name@decor.vn"
        autocomplete="email"
        aria-describedby="loginEmailError"
        aria-invalid="<?= isset($errors['email']) ? 'true' : 'false' ?>"
        required
      >
    </div>
    <p id="loginEmailError" class="field-error<?= isset($errors['email']) ? ' is-visible' : '' ?>">
      <?= e($errors['email'] ?? '') ?>
    </p>
  </div>

  <div class="form-group">
    <label for="loginPassword" class="form-label">Mật khẩu</label>
    <div class="input-wrapper input-wrapper--password">
      <input
        type="password"
        id="loginPassword"
        name="password"
        class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>"
        placeholder="Nhập mật khẩu của bạn"
        autocomplete="current-password"
        aria-describedby="loginPasswordError"
        aria-invalid="<?= isset($errors['password']) ? 'true' : 'false' ?>"
        required
      >
      <button
        type="button"
        class="password-toggle-btn"
        data-toggle-password="loginPassword"
        aria-label="Hiện mật khẩu"
        aria-pressed="false"
      >
        <svg class="icon-eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
          <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
          <line x1="1" y1="1" x2="23" y2="23"/>
        </svg>
        <svg class="icon-eye-open" style="display:none" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
          <circle cx="12" cy="12" r="3"/>
        </svg>
      </button>
    </div>
    <p id="loginPasswordError" class="field-error<?= isset($errors['password']) ? ' is-visible' : '' ?>">
      <?= e($errors['password'] ?? '') ?>
    </p>
  </div>

  <div class="form-row-between">
    <label class="checkbox-label">
      <input type="checkbox" name="remember" class="checkbox-input">
      <span>Ghi nhớ đăng nhập</span>
    </label>
  </div>

  <button type="submit" class="btn-auth-primary">Đăng nhập</button>
</form>
