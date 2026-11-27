<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>
<h2 class="auth-heading">Tạo tài khoản của bạn</h2>
<p class="auth-subheading">Bắt đầu kết nối với không gian nội thất Decor.</p>

<div id="registerAlert" class="auth-alert auth-alert--error<?= !empty($flashError) ? ' is-visible' : '' ?>" role="alert">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <circle cx="12" cy="12" r="10"/>
    <line x1="12" y1="8" x2="12" y2="12"/>
    <line x1="12" y1="16" x2="12.01" y2="16"/>
  </svg>
  <div data-alert-text><?= e($flashError ?? '') ?></div>
</div>

<form id="registerForm" class="auth-form" action="<?= e(base_url('/register')) ?>" method="post" novalidate>
  <?= csrf_field() ?>

  <div class="form-group">
    <label for="regFullName" class="form-label">Họ và tên</label>
    <div class="input-wrapper">
      <input
        type="text"
        id="regFullName"
        name="full_name"
        class="form-control<?= isset($errors['full_name']) ? ' is-invalid' : '' ?>"
        value="<?= e($old['full_name'] ?? '') ?>"
        placeholder="Ví dụ: Lê Thu Hà"
        autocomplete="name"
        required
      >
    </div>
    <p id="regFullNameError" class="field-error<?= isset($errors['full_name']) ? ' is-visible' : '' ?>">
      <?= e($errors['full_name'] ?? '') ?>
    </p>
  </div>

  <div class="form-group">
    <label for="regEmail" class="form-label">Địa chỉ email</label>
    <div class="input-wrapper">
      <input
        type="email"
        id="regEmail"
        name="email"
        class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
        value="<?= e($old['email'] ?? '') ?>"
        placeholder="thuha@decor.vn"
        autocomplete="email"
        required
      >
    </div>
    <p id="regEmailError" class="field-error<?= isset($errors['email']) ? ' is-visible' : '' ?>">
      <?= e($errors['email'] ?? '') ?>
    </p>
  </div>

  <div class="form-group">
    <label for="regPassword" class="form-label">Mật khẩu</label>
    <div class="input-wrapper input-wrapper--password">
      <input
        type="password"
        id="regPassword"
        name="password"
        class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>"
        placeholder="Tối thiểu 8 ký tự"
        autocomplete="new-password"
        required
      >
      <button
        type="button"
        class="password-toggle-btn"
        data-toggle-password="regPassword"
        aria-label="Hiện mật khẩu"
      >
        <svg class="icon-eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
          <line x1="1" y1="1" x2="23" y2="23"/>
        </svg>
        <svg class="icon-eye-open" style="display:none" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
          <circle cx="12" cy="12" r="3"/>
        </svg>
      </button>
    </div>
    <div id="regPasswordHint" class="field-hint">
      <span>&#10003;</span>
      <span>Ít nhất 8 ký tự, gồm chữ hoa, chữ thường và số.</span>
    </div>
    <p id="regPasswordError" class="field-error<?= isset($errors['password']) ? ' is-visible' : '' ?>">
      <?= e($errors['password'] ?? '') ?>
    </p>
  </div>

  <div class="form-group">
    <label for="regPasswordConfirm" class="form-label">Nhập lại mật khẩu</label>
    <div class="input-wrapper input-wrapper--password">
      <input
        type="password"
        id="regPasswordConfirm"
        name="password_confirmation"
        class="form-control<?= isset($errors['password_confirmation']) ? ' is-invalid' : '' ?>"
        placeholder="Nhập lại mật khẩu phía trên"
        autocomplete="new-password"
        required
      >
      <button
        type="button"
        class="password-toggle-btn"
        data-toggle-password="regPasswordConfirm"
        aria-label="Hiện mật khẩu nhập lại"
      >
        <svg class="icon-eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
          <line x1="1" y1="1" x2="23" y2="23"/>
        </svg>
        <svg class="icon-eye-open" style="display:none" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
          <circle cx="12" cy="12" r="3"/>
        </svg>
      </button>
    </div>
    <p id="regPasswordConfirmError" class="field-error<?= isset($errors['password_confirmation']) ? ' is-visible' : '' ?>">
      <?= e($errors['password_confirmation'] ?? '') ?>
    </p>
  </div>

  <div class="form-group">
    <label class="checkbox-label">
      <input type="checkbox" id="regTerms" name="terms" value="1" class="checkbox-input" checked>
      <span>Tôi đồng ý với Điều khoản sử dụng và Chính sách quyền riêng tư của Decor.</span>
    </label>
    <p id="regTermsError" class="field-error<?= isset($errors['terms']) ? ' is-visible' : '' ?>">
      <?= e($errors['terms'] ?? '') ?>
    </p>
  </div>

  <button type="submit" class="btn-auth-primary">Tạo tài khoản</button>

  <div class="auth-role-note">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="12" cy="12" r="10"/>
      <line x1="12" y1="16" x2="12" y2="12"/>
      <line x1="12" y1="8" x2="12.01" y2="8"/>
    </svg>
    <span>Tài khoản mới có vai trò <strong>Thành viên</strong>. Quản trị viên có thể thay đổi quyền truy cập sau.</span>
  </div>
</form>
