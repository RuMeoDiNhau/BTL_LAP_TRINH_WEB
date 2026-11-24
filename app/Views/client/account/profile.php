<div class="page-heading">
    <p class="eyebrow">TÀI KHOẢN CỦA BẠN</p>
    <h1>Hồ sơ cá nhân</h1>
    <p>Chăm chút những thông tin giúp Decor hiểu bạn hơn.</p>
</div>
<div class="account-grid">
    <?php require __DIR__ . '/sidebar.php'; ?>
    <div class="account-content">
        <section class="card" aria-labelledby="avatarHeading">
            <div class="card-heading"><h2 id="avatarHeading">Ảnh đại diện</h2><span class="muted">Dấu ấn của riêng bạn</span></div>
            <form class="avatar-form" action="<?= e(url('/account/avatar')) ?>" method="post" enctype="multipart/form-data" data-avatar-form>
                <?= csrf_field() ?>
                <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
                <div class="avatar avatar--large" data-avatar-preview>
                    <?php if ($avatar = avatar_url($user['avatar'])): ?>
                        <img src="<?= e($avatar) ?>" alt="Ảnh đại diện hiện tại" width="96" height="96">
                    <?php else: ?>
                        <span><?= e(initials($user['full_name'])) ?></span>
                    <?php endif; ?>
                </div>
                <div class="avatar-controls">
                    <label class="field-label" for="avatar">Chọn ảnh từ thiết bị</label>
                    <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" aria-describedby="avatarHint avatarError">
                    <p id="avatarHint" class="field-hint">JPG, PNG hoặc WebP. Tối đa 2 MB và 4096 × 4096 pixel.</p>
                    <p id="avatarError" class="field-error" role="alert"><?= e($errors['avatar'] ?? '') ?></p>
                    <div class="button-row">
                        <button class="button button--small" type="submit" data-avatar-submit>Lưu ảnh đại diện</button>
                        <button class="text-button" type="button" data-avatar-revert hidden>Hủy ảnh đã chọn</button>
                        <?php if ($user['avatar']): ?>
                            <button class="text-button text-button--danger" type="submit" name="remove_avatar" value="1" formnovalidate>Xóa ảnh</button>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </section>

        <section class="card" aria-labelledby="profileHeading">
            <div class="card-heading"><div><h2 id="profileHeading">Thông tin cá nhân</h2><p class="muted">Cập nhật thông tin liên hệ của bạn.</p></div><span class="required-note">* Bắt buộc</span></div>
            <?php if (array_diff_key($errors, ['avatar' => true])): ?>
                <div class="notice notice--error" role="alert">Chưa thể lưu thông tin. Vui lòng kiểm tra các trường bên dưới.</div>
            <?php endif; ?>
            <form action="<?= e(url('/account/profile')) ?>" method="post" data-validate="profile" data-current-email="<?= e($user['email']) ?>">
                <?= csrf_field() ?>
                <div class="field">
                    <label class="field-label" for="full_name">Họ và tên <span aria-hidden="true">*</span></label>
                    <input id="full_name" name="full_name" value="<?= e($old['full_name']) ?>" autocomplete="name" required minlength="2" maxlength="100" aria-describedby="full_nameError" <?= isset($errors['full_name']) ? 'aria-invalid="true"' : '' ?>>
                    <p id="full_nameError" class="field-error"><?= e($errors['full_name'] ?? '') ?></p>
                </div>
                <div class="field-grid">
                    <div class="field">
                        <label class="field-label" for="email">Địa chỉ email <span aria-hidden="true">*</span></label>
                        <input id="email" name="email" type="email" value="<?= e($old['email']) ?>" autocomplete="email" required maxlength="150" aria-describedby="emailHint emailError" <?= isset($errors['email']) ? 'aria-invalid="true"' : '' ?>>
                        <p id="emailHint" class="field-hint">Đây cũng là email dùng để đăng nhập.</p>
                        <p id="emailError" class="field-error"><?= e($errors['email'] ?? '') ?></p>
                    </div>
                    <div class="field">
                        <label class="field-label" for="phone">Số điện thoại</label>
                        <input id="phone" name="phone" type="tel" value="<?= e($old['phone'] ?? '') ?>" autocomplete="tel" maxlength="16" placeholder="Ví dụ: 0912345678" aria-describedby="phoneError" <?= isset($errors['phone']) ? 'aria-invalid="true"' : '' ?>>
                        <p id="phoneError" class="field-error"><?= e($errors['phone'] ?? '') ?></p>
                    </div>
                </div>
                <div class="field email-confirmation" data-email-confirmation>
                    <label class="field-label" for="email_password">Mật khẩu hiện tại để đổi email</label>
                    <div class="password-control"><input id="email_password" name="email_password" type="password" autocomplete="current-password" aria-describedby="emailPasswordHint email_passwordError" <?= isset($errors['email_password']) ? 'aria-invalid="true"' : '' ?>><button type="button" data-toggle-password="email_password" aria-label="Hiện mật khẩu" aria-pressed="false">Hiện</button></div>
                    <p id="emailPasswordHint" class="field-hint">Chỉ cần nhập khi bạn thay đổi địa chỉ email.</p>
                    <p id="email_passwordError" class="field-error"><?= e($errors['email_password'] ?? '') ?></p>
                </div>
                <div class="form-footer"><span class="muted">Bạn có thể cập nhật thông tin bất cứ lúc nào.</span><button class="button" type="submit">Lưu thay đổi</button></div>
            </form>
        </section>
        <p class="security-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6z"/><path d="m8 12 3 3 5-6"/></svg>Chỉ bạn mới có thể cập nhật hồ sơ cá nhân của mình.</p>
    </div>
</div>
