<div class="auth-page">
    <aside class="auth-intro"><p class="eyebrow">DECOR MEMBERS</p><h1>Chào mừng<br>bạn trở lại.</h1><p>Một tài khoản cho những trải nghiệm mang dấu ấn của bạn.</p><div class="intro-decoration" aria-hidden="true"><span>D</span></div><p class="auth-intro-note">Thông tin cá nhân · Ảnh đại diện · Bảo mật</p></aside>
    <section class="card auth-card" aria-labelledby="loginHeading">
        <p class="eyebrow">TÀI KHOẢN DECOR</p><h2 id="loginHeading">Đăng nhập</h2><p class="muted auth-description">Đăng nhập để cập nhật hồ sơ cá nhân của bạn.</p>
        <?php if (isset($errors['form'])): ?><div class="notice notice--error" role="alert"><?= e($errors['form']) ?></div><?php endif; ?>
        <form action="<?= e(url('/login')) ?>" method="post" data-validate="login">
            <?= csrf_field() ?>
            <div class="field"><label class="field-label" for="email">Địa chỉ email</label><input id="email" name="email" type="email" autocomplete="email" value="<?= e($old['email'] ?? '') ?>" required maxlength="150" placeholder="ban@example.com" aria-describedby="emailError"><p id="emailError" class="field-error"></p></div>
            <div class="field"><label class="field-label" for="password">Mật khẩu</label><div class="password-control"><input id="password" name="password" type="password" autocomplete="current-password" required aria-describedby="passwordError"><button type="button" data-toggle-password="password" aria-label="Hiện mật khẩu" aria-pressed="false">Hiện</button></div><p id="passwordError" class="field-error"></p></div>
            <button class="button button--full" type="submit">Đăng nhập</button>
        </form>
        <p class="auth-switch">Chưa có tài khoản? <a href="<?= e(url('/register')) ?>">Đăng ký ngay</a></p>
    </section>
</div>
