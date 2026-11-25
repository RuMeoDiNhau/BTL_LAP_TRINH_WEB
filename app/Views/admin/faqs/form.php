<?php
/** Form thêm/sửa FAQ: giữ nguyên name/action/CSRF, chỉ đổi giao diện. */

/**
 * @var array<string, mixed>  $row     Rỗng khi thêm mới
 * @var array<string, string> $errors  Rỗng khi chưa có lỗi
 */

$isEdit = !empty($row['id']);
$action = $isEdit ? url('admin/faqs/' . (int)$row['id'] . '/update') : url('admin/faqs/store');
$question = (string)($row['question'] ?? '');
$answer = (string)($row['answer'] ?? '');
$sortOrder = (int)($row['sort_order'] ?? 0);
$isActive = array_key_exists('is_active', $row) ? (int)$row['is_active'] === 1 : true;

// Controller đã trả lỗi bằng tiếng Việt; giữ $tr để dùng chung với phần còn lại.
$tr = static function (string $msg): string {
    return $msg;
};
$answerLength = function_exists('mb_strlen') ? mb_strlen($answer, 'UTF-8') : strlen($answer);
?>
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800"><?= e($isEdit ? 'Sửa FAQ' : 'Thêm FAQ') ?></h1>
            <p class="mt-1 text-sm text-gray-500">Nhập câu hỏi, câu trả lời, thứ tự hiển thị và trạng thái.</p>
        </div>
        <a href="<?= e(url('admin/faqs')) ?>"
           class="inline-flex h-11 items-center justify-center gap-2 rounded-[10px] border border-gray-300 bg-white px-5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15 6-6 6 6 6"/>
            </svg>
            Về danh sách
        </a>
    </div>

    <form method="post" action="<?= e($action) ?>" class="grid gap-6 lg:grid-cols-3" novalidate data-faq-form>
        <?= csrf_field() ?>

        <section class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs lg:col-span-2">
            <div class="border-b border-gray-100 px-6 py-5">
                <h2 class="text-base font-medium text-gray-800">Nội dung FAQ</h2>
                <p class="mt-1 text-sm text-gray-500">Nội dung này sẽ hiển thị cho khách truy cập.</p>
            </div>
            <div class="space-y-6 p-4 sm:p-6">
                <div>
                    <label for="question" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Câu hỏi <span class="text-red-500">*</span>
                    </label>
                    <input id="question" name="question" type="text" required maxlength="255"
                           value="<?= e($question) ?>"
                           aria-invalid="<?= isset($errors['question']) ? 'true' : 'false' ?>"
                           class="h-11 w-full min-w-0 rounded-[10px] border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/20 focus:outline-hidden">
                    <p class="mt-1.5 text-xs text-red-600" data-error="question"><?= e($tr($errors['question'] ?? '')) ?></p>
                </div>

                <div>
                    <div class="mb-1.5 flex items-center justify-between gap-4">
                        <label for="answer" class="block text-sm font-medium text-gray-700">
                            Câu trả lời <span class="text-red-500">*</span>
                        </label>
                        <span class="text-xs text-gray-400"><span data-answer-count><?= (int)$answerLength ?></span> / 5000</span>
                    </div>
                    <textarea id="answer" name="answer" required maxlength="5000" rows="10"
                              aria-invalid="<?= isset($errors['answer']) ? 'true' : 'false' ?>"
                              class="w-full min-h-48 rounded-[10px] border border-gray-300 bg-white px-4 py-3 text-sm shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/20 focus:outline-hidden"><?= e($answer) ?></textarea>
                    <p class="mt-1.5 text-xs text-red-600" data-error="answer"><?= e($tr($errors['answer'] ?? '')) ?></p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 px-6 py-5">
                <button type="submit"
                        class="inline-flex h-11 items-center justify-center gap-2 rounded-[10px] bg-brand-500 px-5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <?= e($isEdit ? 'Lưu thay đổi' : 'Lưu FAQ') ?>
                </button>
                <a href="<?= e(url('admin/faqs')) ?>"
                   class="inline-flex h-11 items-center justify-center rounded-[10px] border border-gray-300 bg-white px-5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden">
                    Hủy
                </a>
            </div>
        </section>

        <section class="h-fit rounded-2xl border border-gray-200 bg-white shadow-theme-xs">
            <div class="border-b border-gray-100 px-6 py-5">
                <h2 class="text-base font-medium text-gray-800">Thiết lập</h2>
                <p class="mt-1 text-sm text-gray-500">Tùy chọn hiển thị.</p>
            </div>
            <div class="space-y-6 p-6">
                <div>
                    <label for="sort_order" class="mb-1.5 block text-sm font-medium text-gray-700">Thứ tự</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" step="1"
                           value="<?= e((string)$sortOrder) ?>"
                           aria-invalid="<?= isset($errors['sort_order']) ? 'true' : 'false' ?>"
                           class="h-11 w-full min-w-0 rounded-[10px] border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/20 focus:outline-hidden">
                    <p class="mt-1.5 text-xs text-red-600" data-error="sort_order"><?= e($tr($errors['sort_order'] ?? '')) ?></p>
                    <p class="mt-1.5 text-xs text-gray-500">Số nhỏ hơn sẽ hiển thị trước.</p>
                </div>

                <div class="border-t border-gray-100 pt-6">
                    <span class="mb-2 block text-sm font-medium text-gray-700">Hiển thị</span>
                    <label for="faqActive" class="inline-flex cursor-pointer items-center gap-3">
                        <span class="relative">
                            <input id="faqActive" type="checkbox" name="is_active" value="1" <?= $isActive ? 'checked' : '' ?>
                                   class="peer sr-only">
                            <span aria-hidden="true"
                                  class="block h-6 w-11 rounded-full bg-gray-300 transition-colors peer-checked:bg-brand-500 peer-focus-visible:ring-3 peer-focus-visible:ring-brand-500/20"></span>
                            <span aria-hidden="true"
                                  class="absolute top-0.5 left-0.5 size-5 rounded-full bg-white shadow-theme-xs transition-transform peer-checked:translate-x-5"></span>
                        </span>
                        <span class="text-sm font-medium text-gray-700" data-active-status><?= $isActive ? 'Đang hiển thị' : 'Đang ẩn' ?></span>
                    </label>
                    <p class="mt-2 text-xs text-gray-500">Hiển thị FAQ này trên trang Câu hỏi thường gặp.</p>
                </div>
            </div>
        </section>
    </form>
</div>
