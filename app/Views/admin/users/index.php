<?php
declare(strict_types=1);

$filters = $filters ?? ['keyword' => '', 'role' => '', 'status' => ''];
$pageUrl = static function (int $number) use ($filters): string {
    return base_url('/admin/users') . '?' . http_build_query([
        'keyword' => $filters['keyword'], 'role' => $filters['role'],
        'status' => $filters['status'], 'page' => $number,
    ]);
};
?>
<div class="ta-page-header">
  <div>
    <h1 class="ta-page-title">Quản lý người dùng</h1>
    <p class="ta-page-subtitle">Xem thông tin, đặt lại mật khẩu và quản lý trạng thái tài khoản thành viên.</p>
  </div>
</div>

<section class="ta-card">
  <div class="ta-card-header">
    <div class="ta-card-title-group">
      <h2 class="ta-card-title">Danh sách tài khoản</h2>
      <span class="ta-badge"><?= (int) $total ?> tài khoản</span>
    </div>
  </div>
  <form method="get" action="<?= e(base_url('/admin/users')) ?>" class="ta-card-body" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
    <label class="ta-sr-only" for="user-keyword">Tìm người dùng</label>
    <input id="user-keyword" name="keyword" type="search" value="<?= e($filters['keyword']) ?>" placeholder="Tên, email hoặc số điện thoại" class="ta-user-filter-control" style="min-width:240px;flex:1;">
    <label class="ta-sr-only" for="user-role">Vai trò</label>
    <select id="user-role" name="role" class="ta-user-filter-control">
      <option value="">Tất cả vai trò</option>
      <option value="member" <?= $filters['role'] === 'member' ? 'selected' : '' ?>>Thành viên</option>
      <option value="admin" <?= $filters['role'] === 'admin' ? 'selected' : '' ?>>Quản trị viên</option>
    </select>
    <label class="ta-sr-only" for="user-status">Trạng thái</label>
    <select id="user-status" name="status" class="ta-user-filter-control">
      <option value="">Tất cả trạng thái</option>
      <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Hoạt động</option>
      <option value="banned" <?= $filters['status'] === 'banned' ? 'selected' : '' ?>>Đã khóa</option>
    </select>
    <button type="submit" class="ta-btn ta-btn--primary">Lọc</button>
    <a class="ta-btn ta-btn--outline" href="<?= e(base_url('/admin/users')) ?>">Xóa lọc</a>
  </form>

  <div class="ta-table-wrap">
    <table class="ta-table">
      <thead><tr><th>Người dùng</th><th>Liên hệ</th><th>Vai trò</th><th>Trạng thái</th><th>Ngày tạo</th><th></th></tr></thead>
      <tbody>
      <?php if (!$users): ?>
        <tr><td colspan="6" style="text-align:center;padding:30px;">Không tìm thấy tài khoản phù hợp.</td></tr>
      <?php else: foreach ($users as $item): ?>
        <tr>
          <td><strong><?= e($item['full_name']) ?></strong><br><span style="color:#667085;">#<?= (int) $item['id'] ?></span></td>
          <td><?= e($item['email']) ?><br><span style="color:#667085;"><?= e($item['phone'] ?? '') ?></span></td>
          <td><span class="ta-badge <?= $item['role'] === 'admin' ? 'ta-badge--admin' : '' ?>"><?= $item['role'] === 'admin' ? 'Quản trị viên' : 'Thành viên' ?></span></td>
          <td><span class="ta-badge <?= $item['status'] === 'active' ? 'ta-badge--success' : 'ta-badge--danger' ?>"><?= $item['status'] === 'active' ? 'Hoạt động' : 'Đã khóa' ?></span></td>
          <td><?= e(date('d/m/Y', strtotime((string) $item['created_at']))) ?></td>
          <td><a class="ta-btn ta-btn--outline ta-btn--sm" href="<?= e(base_url('/admin/users/' . (int) $item['id'])) ?>">Xem</a></td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($totalPages > 1): ?>
  <div class="ta-card-body" style="display:flex;justify-content:flex-end;align-items:center;gap:8px;">
    <?php if ($page > 1): ?><a class="ta-btn ta-btn--outline ta-btn--sm" href="<?= e($pageUrl($page - 1)) ?>">Trước</a><?php endif; ?>
    <span>Trang <?= (int) $page ?> / <?= (int) $totalPages ?></span>
    <?php if ($page < $totalPages): ?><a class="ta-btn ta-btn--outline ta-btn--sm" href="<?= e($pageUrl($page + 1)) ?>">Sau</a><?php endif; ?>
  </div>
  <?php endif; ?>
</section>
