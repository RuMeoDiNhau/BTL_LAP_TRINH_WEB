<aside class="account-sidebar" aria-label="Menu tài khoản">
    <div class="member-card">
        <?php if ($avatar = avatar_url($user['avatar'])): ?>
            <img class="avatar avatar--medium" src="<?= e($avatar) ?>" alt="Ảnh đại diện của <?= e($user['full_name']) ?>" width="64" height="64">
        <?php else: ?>
            <span class="avatar avatar--medium" aria-hidden="true"><?= e(initials($user['full_name'])) ?></span>
        <?php endif; ?>
        <strong><?= e($user['full_name']) ?></strong>
        <span class="member-email"><?= e($user['email']) ?></span>
        <span class="member-role"><?= $user['role'] === 'admin' ? 'Quản trị viên' : 'Thành viên Decor' ?></span>
    </div>
    <nav class="account-nav" aria-label="Cài đặt tài khoản">
        <a href="<?= e(url('/account/profile')) ?>" <?= $active === 'profile' ? 'aria-current="page"' : '' ?>>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/></svg>
            Hồ sơ cá nhân
        </a>
        <a href="<?= e(url('/account/change-password')) ?>" <?= $active === 'password' ? 'aria-current="page"' : '' ?>>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V6a4 4 0 0 1 8 0v4"/><path d="M12 14v3"/></svg>
            Đổi mật khẩu
        </a>
    </nav>
    <p class="sidebar-note">Thông tin của bạn được sử dụng để cá nhân hóa trải nghiệm tại Decor.</p>
</aside>
