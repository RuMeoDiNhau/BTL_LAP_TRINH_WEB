<?php
declare(strict_types=1);

$statusLabels = ['unread' => 'Chưa đọc', 'read' => 'Đã đọc', 'replied' => 'Đã phản hồi'];
$pageUrl = static function (int $number) use ($filters): string {
    return base_url('/admin/contacts') . '?' . http_build_query([
        'keyword' => $filters['keyword'], 'status' => $filters['status'], 'page' => $number,
    ]);
};
?>
<div class="ta-page-header">
  <div>
    <h1 class="ta-page-title">Liên hệ khách hàng</h1>
    <p class="ta-page-subtitle">Xem và cập nhật trạng thái các liên hệ gửi từ website.</p>
  </div>
</div>

<section class="ta-card">
  <div class="ta-card-header"><div class="ta-card-title-group"><h2 class="ta-card-title">Danh sách liên hệ</h2><span class="ta-badge"><?= (int) $total ?> liên hệ</span></div></div>
  <form action="<?= e(base_url('/admin/contacts')) ?>" method="get" class="ta-card-body ta-contact-filters">
    <label class="ta-visually-hidden" for="contact-keyword">Tìm liên hệ</label>
    <input id="contact-keyword" name="keyword" type="search" value="<?= e($filters['keyword']) ?>" placeholder="Tìm tên, email hoặc nội dung">
    <label class="ta-visually-hidden" for="contact-status">Trạng thái</label>
    <select id="contact-status" name="status">
      <option value="">Tất cả trạng thái</option>
      <?php foreach ($statusLabels as $value => $label): ?><option value="<?= e($value) ?>" <?= $filters['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
    </select>
    <button type="submit" class="ta-btn ta-btn--primary">Lọc</button>
    <a href="<?= e(base_url('/admin/contacts')) ?>" class="ta-btn ta-btn--outline">Xóa lọc</a>
  </form>

  <div class="ta-table-wrap">
    <table class="ta-table">
      <thead><tr><th>Khách hàng</th><th>Liên hệ</th><th>Nội dung</th><th>Trạng thái</th><th>Ngày gửi</th><th>Thao tác</th></tr></thead>
      <tbody>
      <?php if (!$contacts): ?>
        <tr><td colspan="6" class="ta-center">Chưa có liên hệ phù hợp.</td></tr>
      <?php else: foreach ($contacts as $contact): ?>
        <tr>
          <td><strong><?= e($contact['name']) ?></strong></td>
          <td><?= e($contact['email']) ?><br><?= e($contact['phone'] ?? '') ?></td>
          <td><strong><?= e($contact['subject'] ?? 'Không có chủ đề') ?></strong><br><span class="ta-contact-message"><?= nl2br(e($contact['message'])) ?></span></td>
          <td><span class="ta-badge ta-status--<?= e($contact['status']) ?>"><?= e($statusLabels[$contact['status']] ?? 'Không xác định') ?></span></td>
          <td><?= e(date('d/m/Y H:i', strtotime((string) $contact['created_at']))) ?></td>
          <td>
            <div class="ta-contact-actions">
              <?php foreach ($statusLabels as $value => $label): if ($contact['status'] === $value) { continue; } ?>
              <form action="<?= e(base_url('/admin/contacts/' . (int) $contact['id'] . '/status')) ?>" method="post">
                <?= csrf_field() ?><input type="hidden" name="status" value="<?= e($value) ?>">
                <button class="ta-btn ta-btn--outline ta-btn--sm" type="submit"><?= e($label) ?></button>
              </form>
              <?php endforeach; ?>
              <form action="<?= e(base_url('/admin/contacts/' . (int) $contact['id'] . '/delete')) ?>" method="post" onsubmit="return confirm('Xóa liên hệ này?');">
                <?= csrf_field() ?><button class="ta-btn ta-btn--danger ta-btn--sm" type="submit">Xóa</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($totalPages > 1): ?>
  <div class="ta-card-body ta-contact-pagination">
    <?php if ($page > 1): ?><a class="ta-btn ta-btn--outline ta-btn--sm" href="<?= e($pageUrl($page - 1)) ?>">Trước</a><?php endif; ?>
    <span>Trang <?= (int) $page ?> / <?= (int) $totalPages ?></span>
    <?php if ($page < $totalPages): ?><a class="ta-btn ta-btn--outline ta-btn--sm" href="<?= e($pageUrl($page + 1)) ?>">Sau</a><?php endif; ?>
  </div>
  <?php endif; ?>
</section>
