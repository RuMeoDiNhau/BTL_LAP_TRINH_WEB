<?php
$adminCount = (int) ($roleCounts['admin'] ?? 0);
$memberCount = (int) ($roleCounts['member'] ?? 0);
?>
<div class="ta-page-header">
  <div>
    <h1 class="ta-page-title">Vai trò &amp; quyền hạn</h1>
    <p class="ta-page-subtitle">Phân quyền truy cập theo 2 vai trò chuẩn trong cơ sở dữ liệu Decor (<code>users.role</code>).</p>
  </div>
  <div class="ta-header-buttons">
    <span class="ta-badge ta-badge--admin">
      Đang đăng nhập: <?= e($currentUser['full_name'] ?? 'Quản trị viên') ?> (admin)
    </span>
  </div>
</div>

<div class="ta-section-head">
  <span class="ta-section-label">Vai trò trong hệ thống Decor</span>
  <span class="ta-section-hint">Chọn một vai trò để xem chi tiết ma trận quyền hạn</span>
</div>

<section class="ta-role-cards-grid" aria-label="Danh sách vai trò">
  <button type="button" class="ta-role-card is-selected" data-role-card="admin" aria-pressed="true">
    <div class="ta-role-card-top">
      <div class="ta-role-card-title-wrap">
        <span class="ta-stat-icon" aria-hidden="true">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
          </svg>
        </span>
        <div>
          <div class="ta-role-name">Quản trị viên (admin)</div>
          <div class="ta-role-user-count"><?= $adminCount ?> tài khoản</div>
        </div>
      </div>
      <span class="ta-role-radio" aria-hidden="true"></span>
    </div>
    <p class="ta-role-desc">
      Toàn quyền quản trị người dùng, sản phẩm, đơn hàng, tin tức, bình luận và cấu hình website.
    </p>
    <div class="ta-role-perm-summary">20/20 quyền được cấp</div>
  </button>

  <button type="button" class="ta-role-card" data-role-card="member" aria-pressed="false">
    <div class="ta-role-card-top">
      <div class="ta-role-card-title-wrap">
        <span class="ta-stat-icon" aria-hidden="true">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </span>
        <div>
          <div class="ta-role-name">Thành viên (member)</div>
          <div class="ta-role-user-count"><?= $memberCount ?> tài khoản</div>
        </div>
      </div>
      <span class="ta-role-radio" aria-hidden="true"></span>
    </div>
    <p class="ta-role-desc">
      Xem nội dung công khai, đặt mua sản phẩm và gửi bình luận bài viết.
    </p>
    <div class="ta-role-perm-summary">6/20 quyền được cấp</div>
  </button>
</section>

<section class="ta-card" aria-label="Ma trận quyền hạn theo nhóm chức năng">
  <div class="ta-card-header">
    <div>
      <div class="ta-card-title-group">
        <h2 class="ta-card-title" id="matrixRoleTitle">Quyền của vai trò Quản trị viên (admin)</h2>
        <span class="ta-count-pill" id="matrixModeBadge">20/20 quyền được cấp &bull; Chỉ xem</span>
      </div>
      <p class="ta-page-subtitle" style="margin-top:4px;">
        Bảng tổng hợp quyền truy cập theo từng module của hệ thống Decor E-Commerce.
      </p>
    </div>

    <div class="ta-perm-legend">
      <span class="ta-perm-legend-item">
        <span class="ta-perm-box ta-perm-box--allowed">&#10003;</span>
        <span>Được phép</span>
      </span>
      <span class="ta-perm-legend-item">
        <span class="ta-perm-box ta-perm-box--denied">&minus;</span>
        <span>Bị giới hạn</span>
      </span>
    </div>
  </div>

  <div class="ta-table-wrap">
    <table class="ta-table">
      <thead>
        <tr>
          <th>Nhóm chức năng</th>
          <th class="ta-center" style="width:120px;">Xem</th>
          <th class="ta-center" style="width:120px;">Tạo</th>
          <th class="ta-center" style="width:120px;">Sửa</th>
          <th class="ta-center" style="width:120px;">Xóa</th>
        </tr>
      </thead>
      <tbody id="permissionMatrixBody"></tbody>
    </table>
  </div>

  <div class="ta-card-footer">
    <span style="display:inline-flex;align-items:center;gap:6px;">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="16" x2="12" y2="12"/>
        <line x1="12" y1="8" x2="12.01" y2="8"/>
      </svg>
      Chỉ tài khoản có <code>role = 'admin'</code> mới được phép truy cập các đường dẫn <code>/admin/*</code>.
    </span>
  </div>
</section>
