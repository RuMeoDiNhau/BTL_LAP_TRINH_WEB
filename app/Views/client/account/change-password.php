<div class="page-heading"><p class="eyebrow">TÀI KHOẢN CỦA BẠN</p><h1>Đổi mật khẩu</h1><p>Giữ tài khoản an toàn với một mật khẩu riêng biệt.</p></div>
<div class="account-grid">
    <?php require __DIR__ . '/sidebar.php'; ?>
    <section class="card" aria-labelledby="passwordHeading">
        <div class="card-heading"><div><h2 id="passwordHeading">Bảo mật tài khoản</h2><p class="muted">Xác nhận mật khẩu hiện tại trước khi cập nhật.</p></div></div>
        <?php if ($errors): ?><div class="notice notice--error" role="alert">Chưa thể đổi mật khẩu. Vui lòng kiểm tra thông tin bên dưới.</div><?php endif; ?>
        <form action="<?= e(url('/account/change-password')) ?>" method="post" data-validate="password">
            <?= csrf_field() ?>
            <div class="field">
                <label class="field-label" for="current_password">Mật khẩu hiện tại <span aria-hidden="true">*</span></label>
                <div class="password-control"><input id="current_password" name="current_password" type="password" autocomplete="current-password" required aria-describedby="current_passwordError" <?= isset($errors['current_password']) ? 'aria-invalid="true"' : '' ?>><button type="button" data-toggle-password="current_password" aria-label="Hiện mật khẩu hiện tại" aria-pressed="false">Hiện</button></div>
                <p id="current_passwordError" class="field-error"><?= e($errors['current_password'] ?? '') ?></p>
            </div>
            <div class="field">
                <label class="field-label" for="password">Mật khẩu mới <span aria-hidden="true">*</span></label>
                <div class="password-control"><input id="password" name="password" type="password" autocomplete="new-password" required minlength="8" maxlength="72" aria-describedby="passwordRules passwordError" <?= isset($errors['password']) ? 'aria-invalid="true"' : '' ?>><button type="button" data-toggle-password="password" aria-label="Hiện mật khẩu mới" aria-pressed="false">Hiện</button></div>
                <p id="passwordError" class="field-error"><?= e($errors['password'] ?? '') ?></p>
                <ul id="passwordRules" class="password-rules" aria-label="Yêu cầu mật khẩu">
                    <li data-password-rule="length">Từ 8 ký tự, tối đa 72 byte</li><li data-password-rule="case">Có chữ hoa và chữ thường</li><li data-password-rule="number">Có ít nhất một chữ số</li><li data-password-rule="different">Khác mật khẩu hiện tại</li>
                </ul>
            </div>
            <div class="field">
                <label class="field-label" for="password_confirmation">Nhập lại mật khẩu mới <span aria-hidden="true">*</span></label>
                <div class="password-control"><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required maxlength="72" aria-describedby="password_confirmationError" <?= isset($errors['password_confirmation']) ? 'aria-invalid="true"' : '' ?>><button type="button" data-toggle-password="password_confirmation" aria-label="Hiện mật khẩu nhập lại" aria-pressed="false">Hiện</button></div>
                <p id="password_confirmationError" class="field-error"><?= e($errors['password_confirmation'] ?? '') ?></p>
            </div>
            <div class="notice notice--info">Sau khi đổi mật khẩu, bạn tiếp tục sử dụng phiên hiện tại. Các phiên đăng nhập khác sẽ cần đăng nhập lại.</div>
            <div class="form-footer"><a class="text-link" href="<?= e(url('/account/profile')) ?>">Về hồ sơ cá nhân</a><button class="button" type="submit">Cập nhật mật khẩu</button></div>
        </form>
    </section>
</div>
