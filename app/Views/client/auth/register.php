<div class="auth-page">
    <aside class="auth-intro"><p class="eyebrow">DECOR MEMBERS</p><h1>Không gian riêng.<br>Bắt đầu từ bạn.</h1><p>Tạo tài khoản để lưu thông tin và chăm chút trải nghiệm của bạn tại Decor.</p><div class="intro-decoration" aria-hidden="true"><span>D</span></div><p class="auth-intro-note">Thông tin cá nhân · Ảnh đại diện · Bảo mật</p></aside>
    <section class="card auth-card" aria-labelledby="registerHeading">
        <p class="eyebrow">GIA NHẬP DECOR</p><h2 id="registerHeading">Tạo tài khoản</h2><p class="muted auth-description">Một vài thông tin để bắt đầu.</p>
        <?php if ($errors): ?><div class="notice notice--error" role="alert">Chưa thể tạo tài khoản. Vui lòng kiểm tra thông tin bên dưới.</div><?php endif; ?>
        <form action="<?= e(url('/register')) ?>" method="post" data-validate="register">
            <?= csrf_field() ?>
            <div class="field"><label class="field-label" for="full_name">Họ và tên</label><input id="full_name" name="full_name" autocomplete="name" required minlength="2" maxlength="100" value="<?= e($old['full_name'] ?? '') ?>" aria-describedby="full_nameError"><p id="full_nameError" class="field-error"><?= e($errors['full_name'] ?? '') ?></p></div>
            <div class="field"><label class="field-label" for="email">Địa chỉ email</label><input id="email" name="email" type="email" autocomplete="email" required maxlength="150" value="<?= e($old['email'] ?? '') ?>" aria-describedby="emailError"><p id="emailError" class="field-error"><?= e($errors['email'] ?? '') ?></p></div>
            <div class="field"><label class="field-label" for="password">Mật khẩu</label><div class="password-control"><input id="password" name="password" type="password" autocomplete="new-password" required minlength="8" maxlength="72" aria-describedby="passwordHint passwordError"><button type="button" data-toggle-password="password" aria-label="Hiện mật khẩu" aria-pressed="false">Hiện</button></div><p id="passwordHint" class="field-hint">Từ 8 ký tự, tối đa 72 byte; có chữ hoa, chữ thường và số.</p><p id="passwordError" class="field-error"><?= e($errors['password'] ?? '') ?></p></div>
            <div class="field"><label class="field-label" for="password_confirmation">Nhập lại mật khẩu</label><div class="password-control"><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required maxlength="72" aria-describedby="password_confirmationError"><button type="button" data-toggle-password="password_confirmation" aria-label="Hiện mật khẩu nhập lại" aria-pressed="false">Hiện</button></div><p id="password_confirmationError" class="field-error"><?= e($errors['password_confirmation'] ?? '') ?></p></div>
            <button class="button button--full" type="submit">Tạo tài khoản</button>
        </form>
        <p class="auth-switch">Đã có tài khoản? <a href="<?= e(url('/login')) ?>">Đăng nhập</a></p>
    </section>
</div>
