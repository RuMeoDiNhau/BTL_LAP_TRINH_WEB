<?php
declare(strict_types=1);
$member = ($managedUser['role'] ?? '') === 'member';
$isSelf = (int) ($managedUser['id'] ?? 0) === (int) ($adminId ?? 0);
?>
<div class="ta-page-header">
  <div>
    <h1 class="ta-page-title">Thông tin người dùng</h1>
    <p class="ta-page-subtitle">Tài khoản #<?= (int) $managedUser['id'] ?></p>
  </div>
  <a class="ta-btn ta-btn--outline" href="<?= e(base_url('/admin/users')) ?>">Quay lại danh sách</a>
</div>

<section class="ta-card">
  <div class="ta-card-header"><h2 class="ta-card-title">Thông tin tài khoản</h2></div>
  <div class="ta-card-body">
    <dl class="ta-user-details">
      <div><dt class="ta-user-detail-label">Họ và tên</dt><dd class="ta-user-detail-value"><?= e($managedUser['full_name']) ?></dd></div>
      <div><dt class="ta-user-detail-label">Email</dt><dd class="ta-user-detail-value"><?= e($managedUser['email']) ?></dd></div>
      <div><dt class="ta-user-detail-label">Số điện thoại</dt><dd class="ta-user-detail-value"><?= e($managedUser['phone'] ?? '') ?: '—' ?></dd></div>
      <div><dt class="ta-user-detail-label">Vai trò</dt><dd class="ta-user-detail-value"><?= $member ? 'Thành viên' : 'Quản trị viên' ?></dd></div>
      <div><dt class="ta-user-detail-label">Trạng thái</dt><dd class="ta-user-detail-value"><?= $managedUser['status'] === 'active' ? 'Hoạt động' : 'Đã khóa' ?></dd></div>
      <div><dt class="ta-user-detail-label">Ngày tạo</dt><dd class="ta-user-detail-value"><?= e(date('d/m/Y H:i', strtotime((string) $managedUser['created_at']))) ?></dd></div>
    </dl>
  </div>
</section>

<?php if ($member): ?>
<section class="ta-card" style="margin-top:20px;">
  <div class="ta-card-header"><h2 class="ta-card-title">Thao tác tài khoản</h2></div>
  <div class="ta-card-body ta-user-actions">
    <form method="post" action="<?= e(base_url('/admin/users/' . (int) $managedUser['id'] . '/reset-password')) ?>" onsubmit="return confirm('Đặt lại mật khẩu cho tài khoản này?');">
      <?= csrf_field() ?>
      <button type="submit" class="ta-btn ta-btn--primary">Đặt lại mật khẩu</button>
    </form>
    <?php if (!$isSelf): ?>
    <form method="post" action="<?= e(base_url('/admin/users/' . (int) $managedUser['id'] . '/status')) ?>" onsubmit="return confirm('Bạn có chắc muốn <?= $managedUser['status'] === 'active' ? 'khóa' : 'mở khóa' ?> tài khoản này?');">
      <?= csrf_field() ?>
      <input type="hidden" name="status" value="<?= $managedUser['status'] === 'active' ? 'banned' : 'active' ?>">
      <button type="submit" class="ta-btn ta-btn--outline"><?= $managedUser['status'] === 'active' ? 'Khóa tài khoản' : 'Mở khóa tài khoản' ?></button>
    </form>
    <?php endif; ?>
    <p class="ta-user-actions-note">Mật khẩu tạm chỉ được hiển thị một lần sau khi đặt lại. Người dùng cần đăng nhập lại bằng mật khẩu mới.</p>
  </div>
</section>
<?php endif; ?>
