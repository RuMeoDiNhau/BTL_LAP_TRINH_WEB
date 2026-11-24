<?php
/** Chi tiết câu hỏi FAQ: nội dung, thông tin liên hệ, đổi trạng thái, xoá. */
?>
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Câu hỏi khách hàng #<?= (int)$row['id'] ?></h1>
            <p class="mt-1 text-sm text-gray-500">Được gửi từ trang FAQ.</p>
        </div>
        <a href="<?= e(url('admin/faq-questions')) ?>"
           class="inline-flex h-11 items-center justify-center gap-2 rounded-[10px] border border-gray-300 bg-white px-5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15 6-6 6 6 6"/>
            </svg>
            Về danh sách
        </a>
    </div>

    <section aria-label="Nội dung câu hỏi" class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs">
        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-5">
            <h2 class="text-base font-medium text-gray-800">Tin nhắn</h2>
            <?php if ($row['status'] === 'unread'): ?>
                <span class="inline-flex h-6 w-fit shrink-0 items-center justify-center gap-1 rounded-lg bg-amber-500 px-2.5 py-0.5 text-sm font-medium whitespace-nowrap text-white">Chưa đọc</span>
            <?php elseif ($row['status'] === 'read'): ?>
                <span class="inline-flex h-6 w-fit shrink-0 items-center justify-center gap-1 rounded-lg bg-red-500 px-2.5 py-0.5 text-sm font-medium whitespace-nowrap text-white">Đã đọc</span>
            <?php else: ?>
                <span class="inline-flex h-6 w-fit shrink-0 items-center justify-center gap-1 rounded-lg bg-green-500 px-2.5 py-0.5 text-sm font-medium whitespace-nowrap text-white">Đã trả lời</span>
            <?php endif; ?>
        </div>
        <div class="border-t border-gray-100 p-4 sm:p-6">
            <h3 class="text-sm font-medium text-gray-500">Chủ đề</h3>
            <p class="mt-1 text-base text-gray-800"><?= e((string)$row['subject']) ?></p>
            <h3 class="mt-6 text-sm font-medium text-gray-500">Nội dung tin nhắn</h3>
            <div class="mt-1 text-sm leading-6 text-gray-700"><?= nl2br(e((string)$row['message'])) ?></div>
        </div>
    </section>

    <section aria-label="Thông tin liên hệ" class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs">
        <div class="px-6 py-5">
            <h2 class="text-base font-medium text-gray-800">Thông tin liên hệ</h2>
            <p class="mt-1 text-sm text-gray-500">Dùng các thông tin này để trả lời khách hàng.</p>
        </div>
        <div class="border-t border-gray-100 p-4 sm:p-6">
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Họ và tên</dt>
                    <dd class="mt-1 text-sm text-gray-800"><?= e((string)$row['name']) ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-800"><?= e((string)$row['email']) ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Điện thoại</dt>
                    <dd class="mt-1 text-sm text-gray-800"><?= e((string)($row['phone'] ?? '')) ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Thời gian nhận</dt>
                    <dd class="mt-1 text-sm text-gray-800"><?= e((string)$row['created_at']) ?></dd>
                </div>
            </dl>
        </div>
    </section>

    <div class="flex flex-wrap items-center gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-xs sm:p-6">
        <form method="post" action="<?= e(url('admin/faq-questions/status')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
            <input type="hidden" name="status" value="replied">
            <button type="submit"
                    class="inline-flex h-11 items-center justify-center rounded-[10px] bg-brand-500 px-5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden">
                Đánh dấu đã trả lời
            </button>
        </form>
        <form method="post" action="<?= e(url('admin/faq-questions/status')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
            <input type="hidden" name="status" value="unread">
            <button type="submit"
                    class="inline-flex h-11 items-center justify-center rounded-[10px] border border-gray-300 bg-white px-5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden">
                Đánh dấu chưa đọc
            </button>
        </form>
        <button type="button" title="Xóa câu hỏi" aria-label="Xóa câu hỏi"
                data-question-delete="<?= (int)$row['id'] ?>"
                data-question-subject="<?= e((string)$row['subject']) ?>"
                data-question-action="<?= e(url('admin/faq-questions/delete')) ?>"
                class="inline-flex h-11 items-center justify-center gap-2 rounded-[10px] bg-red-600 px-5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-red-700 focus-visible:ring-3 focus-visible:ring-red-500/20 focus-visible:outline-hidden">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"/>
            </svg>
            Xóa
        </button>
    </div>

    <div class="flex items-start gap-3 rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
        <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="1.8" aria-hidden="true">
            <circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 11v5m0-8h.01"/>
        </svg>
        <p>Hãy trả lời khách hàng qua thông tin liên hệ ở trên; hệ thống này không tự gửi email trả lời.</p>
    </div>
</div>

<dialog id="questionDeleteDialog" aria-labelledby="questionDeleteTitle"
        class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xl">
    <div class="flex items-start justify-between gap-4">
        <span class="flex size-11 items-center justify-center rounded-full bg-red-50 text-red-600">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"/>
            </svg>
        </span>
        <button type="button" data-question-dialog-close aria-label="Đóng hộp thoại"
                class="inline-flex size-9 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/>
            </svg>
        </button>
    </div>
    <h2 id="questionDeleteTitle" class="mt-4 text-lg font-semibold text-gray-800">Xóa câu hỏi</h2>
    <p class="mt-2 text-sm text-gray-500">Câu hỏi dưới đây sẽ bị xóa vĩnh viễn. Bạn không thể hoàn tác thao tác này.</p>
    <p data-question-subject class="mt-4 rounded-xl bg-gray-50 p-3 text-sm font-medium text-gray-800"></p>
    <form method="post" id="questionDeleteForm" action="#" class="mt-6 flex justify-end gap-3">
        <?= csrf_field() ?>
        <input type="hidden" name="id" id="questionDeleteId" value="">
        <button type="button" data-question-dialog-close data-question-dialog-cancel
                class="inline-flex h-11 items-center justify-center rounded-[10px] border border-gray-300 bg-white px-5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden">
            Hủy
        </button>
        <button type="submit"
                class="inline-flex h-11 items-center justify-center rounded-[10px] bg-red-600 px-5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-red-700 focus-visible:ring-3 focus-visible:ring-red-500/20 focus-visible:outline-hidden">
            Xóa
        </button>
    </form>
</dialog>
