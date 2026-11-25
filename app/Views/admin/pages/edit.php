<?php
/** Form sửa nội dung About/FAQ: giữ nguyên mọi name/action/CSRF, chỉ đổi giao diện. */
/**
 * @var string $page
 * @var array  $config
 * @var array  $values
 * @var array  $errors
 */

$pageLabel = (string)($config['label'] ?? 'Trang');
$sections = $config['sections'] ?? [];
$tabs = $config['tabs'] ?? [];
$action = url('admin/pages/' . $page . '/update');

// Controller đã trả lỗi bằng tiếng Việt; giữ $tr để _fields.php dùng chung.
$tr = static function (string $msg): string {
    return $msg;
};

// Gom khối theo tab; tab đầu tiên (hoặc tab chứa lỗi) được mở sẵn.
$grouped = [];
$errorTabs = [];
foreach ($sections as $section) {
    $group = (string)($section['group'] ?? 'banner');
    $grouped[$group][] = $section;
    if (isset($errors[$section['key']])) {
        $errorTabs[$group] = true;
    }
}
$errorTabs = array_keys($errorTabs);
reset($tabs);
$initialTab = (string)key($tabs);
foreach ($sections as $section) {
    if (isset($errors[$section['key']])) {
        $initialTab = (string)($section['group'] ?? $initialTab);
        break;
    }
}

/** Điền giá trị mặc định từ seed cho khối chưa có trong CSDL. */
$valueFor = static function (array $section, array $values, array $errors): array {
    $key = (string)$section['key'];
    $val = $values[$key] ?? ['title' => '', 'content' => '', 'image' => ''];
    $err = $errors[$key] ?? [];
    if ($err !== []) {
        return $val;
    }
    $empty = ($val['title'] ?? '') === '' && ($val['content'] ?? '') === '' && ($val['image'] ?? '') === '';
    if (!$empty) {
        return $val;
    }
    $defaults = $section['default'] ?? [];
    foreach (['title', 'content', 'image'] as $field) {
        if (in_array($field, $section['fields'] ?? [], true) && isset($defaults[$field])) {
            $val[$field] = $defaults[$field];
        }
    }
    return $val;
};

/** Card nhỏ cho từng khối; nhãn/hint luôn lấy từ cấu hình. */
$renderCard = static function (string $key, array $section, array $val, array $err) use ($tr): void {
    $label = (string)($section['label'] ?? $key);
    $hint = (string)($section['hint'] ?? '');
    $fields = $section['fields'] ?? [];
    $hasImage = in_array('image', $fields, true);
    $imagePath = (string)($val['image'] ?? '');
    ?>
    <section class="rounded-2xl border border-gray-200 bg-white shadow-theme-xs"
             data-section="<?= e($key) ?>"
             data-group="<?= e((string)($section['group'] ?? 'banner')) ?>"
             data-fields="<?= e(implode(',', $fields)) ?>">
        <div class="border-b border-gray-100 px-6 py-5">
            <h2 class="text-base font-medium text-gray-800"><?= e($label) ?></h2>
            <?php if ($hint !== ''): ?>
                <p class="mt-1 text-sm text-gray-500"><?= e($hint) ?></p>
            <?php endif; ?>
        </div>
        <div class="space-y-5 p-4 sm:p-6">
            <?php include __DIR__ . '/_fields.php'; ?>

            <?php if ($hasImage): ?>
                <div data-image-block>
                    <span class="mb-1.5 block text-sm font-medium text-gray-700">Hình ảnh</span>
                    <div class="grid gap-4 lg:grid-cols-2">
                        <div>
                            <div class="aspect-4/3 overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
                                <img data-preview
                                     data-original-src="<?= e($imagePath !== '' ? asset($imagePath) : '') ?>"
                                     <?= $imagePath !== '' ? 'src="' . e(asset($imagePath)) . '"' : '' ?>
                                     alt="Xem trước: <?= e($label) ?>"
                                     class="h-full w-full object-cover <?= $imagePath === '' ? 'hidden' : '' ?>">
                                <div data-preview-empty
                                     class="flex h-full w-full items-center justify-center px-2 text-center text-sm text-gray-400 <?= $imagePath !== '' ? 'hidden' : '' ?>">Chưa có hình ảnh</div>
                            </div>
                            <p class="mt-2 text-xs text-gray-400">Hiện tại: <span data-image-path><?= e($imagePath !== '' ? $imagePath : 'không có') ?></span></p>
                        </div>

                        <div class="space-y-3">
                            <label for="<?= e($key) ?>_image" data-dropzone
                                   class="relative flex cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50 p-7 text-center transition-all duration-200 hover:border-brand-400 hover:bg-brand-50 focus-within:border-brand-400 focus-within:ring-3 focus-within:ring-brand-500/20 lg:p-10">
                                <svg class="size-7 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="1.6" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L7 9m5-5 5 5M4 16v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"/>
                                </svg>
                                <span class="mt-2 text-sm font-medium text-gray-700">Kéo thả hoặc nhấn để chọn ảnh</span>
                                <span class="mt-1 text-xs text-gray-500">JPG, PNG, WEBP · tối đa 2 MB</span>
                                <input type="file" id="<?= e($key) ?>_image"
                                       name="sections[<?= e($key) ?>][image]"
                                       accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                       class="sr-only" data-file-input>
                            </label>

                            <p data-file-info hidden class="text-xs text-gray-600"></p>

                            <div class="flex flex-wrap items-center gap-2">
                                <input type="checkbox" id="<?= e($key) ?>_remove_image"
                                       name="sections[<?= e($key) ?>][remove_image]" value="1"
                                       aria-label="Xóa hình ảnh hiện tại"
                                       class="sr-only" data-remove-image>
                                <button type="button" data-remove-button
                                        aria-controls="<?= e($key) ?>_remove_image" aria-pressed="false"
                                        class="inline-flex h-9 items-center gap-2 rounded-[10px] border border-gray-300 bg-white px-3 text-sm font-medium text-red-600 shadow-theme-xs transition hover:border-red-300 hover:bg-red-50 focus-visible:ring-3 focus-visible:ring-red-500/20 focus-visible:outline-hidden disabled:cursor-not-allowed disabled:opacity-50">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2m-9 0 1 13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1l1-13"/>
                                    </svg>
                                    <span data-remove-label>Xóa hình ảnh</span>
                                </button>
                            </div>
                            <p class="text-xs text-red-600" data-error="<?= e($key) ?>.image"><?= e($tr($err['image'] ?? '')) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
};
?>
<div class="space-y-6"
     x-data="{ tab: '<?= e($initialTab) ?>', errorTabs: <?= e(json_encode($errorTabs, JSON_UNESCAPED_UNICODE)) ?> }"
     @page-errors.window="errorTabs = $event.detail.errorTabs; if ($event.detail.tab) tab = $event.detail.tab"
     data-page-editor>
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800"><?= e($pageLabel) ?></h1>
            <p class="mt-1 text-sm text-gray-500">Chỉnh sửa các khối nội dung. Khối chưa có dữ liệu sẽ được điền nội dung mặc định và tạo khi bạn lưu.</p>
        </div>
        <a href="<?= e(url('admin')) ?>"
           class="inline-flex h-11 items-center justify-center gap-2 rounded-[10px] border border-gray-300 bg-white px-5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15 6-6 6 6 6"/>
            </svg>
            Về trang quản trị
        </a>
    </div>

    <form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="space-y-6" novalidate
          data-page-form data-page="<?= e($page) ?>">
        <?= csrf_field() ?>
        <input type="hidden" data-active-tab value="<?= e($initialTab) ?>">

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs">
            <div role="tablist" aria-label="Các khối nội dung trang" class="border-b border-gray-100 px-2 sm:px-4">
                <div class="flex gap-1 overflow-x-auto">
                    <?php foreach ($tabs as $tabId => $tabLabel): ?>
                        <?php $tabId = (string)$tabId; ?>
                        <button type="button" role="tab" id="tab-<?= e($tabId) ?>"
                                aria-controls="panel-<?= e($tabId) ?>"
                                data-tab="<?= e($tabId) ?>"
                                aria-selected="<?= $tabId === $initialTab ? 'true' : 'false' ?>"
                                tabindex="<?= $tabId === $initialTab ? '0' : '-1' ?>"
                                @click="tab = '<?= e($tabId) ?>'"
                                :aria-selected="tab === '<?= e($tabId) ?>' ? 'true' : 'false'"
                                :tabindex="tab === '<?= e($tabId) ?>' ? '0' : '-1'"
                                :class="tab === '<?= e($tabId) ?>'
                                    ? 'relative inline-flex h-12 shrink-0 items-center gap-2 whitespace-nowrap border-b-2 border-brand-500 px-4 text-sm font-medium text-brand-600 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden'
                                    : 'relative inline-flex h-12 shrink-0 items-center gap-2 whitespace-nowrap border-b-2 border-transparent px-4 text-sm font-medium text-gray-500 transition hover:border-gray-300 hover:text-gray-800 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden'">
                            <?= e($tabLabel) ?>
                            <span x-cloak x-show="errorTabs.includes('<?= e($tabId) ?>')"
                                  class="size-1.5 rounded-full bg-red-500" title="Có lỗi cần sửa" aria-hidden="true"></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php foreach ($tabs as $tabId => $tabLabel): ?>
                <?php
                $tabId = (string)$tabId;
                $tabSections = $grouped[$tabId] ?? [];
                $introCount = $tabId === 'banner' ? max(0, count($tabSections) - 1) : 0;
                $introGrid = $introCount >= 3 ? 'grid gap-4 md:grid-cols-3' : 'grid gap-4';
                ?>
                <div role="tabpanel" id="panel-<?= e($tabId) ?>"
                     aria-labelledby="tab-<?= e($tabId) ?>" tabindex="0"
                     x-cloak x-show="tab === '<?= e($tabId) ?>'"
                     class="p-4 sm:p-6">
                    <?php if ($tabId === 'banner'): ?>
                        <?php foreach ($tabSections as $section): ?>
                            <?php
                            $key = (string)$section['key'];
                            if ($key === 'hero') {
                                $renderCard($key, $section, $valueFor($section, $values, $errors), $errors[$key] ?? []);
                            }
                            ?>
                        <?php endforeach; ?>
                        <?php if ($introCount > 0): ?>
                            <div class="<?= $introGrid ?> mt-4">
                                <?php foreach ($tabSections as $section): ?>
                                    <?php
                                    $key = (string)$section['key'];
                                    if ($key !== 'hero') {
                                        $renderCard($key, $section, $valueFor($section, $values, $errors), $errors[$key] ?? []);
                                    }
                                    ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php elseif (in_array($tabId, ['testimonials', 'features'], true)): ?>
                        <div class="grid gap-4 md:grid-cols-2">
                            <?php foreach ($tabSections as $section): ?>
                                <?php
                                $key = (string)$section['key'];
                                $renderCard($key, $section, $valueFor($section, $values, $errors), $errors[$key] ?? []);
                                ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($tabSections as $section): ?>
                                <?php
                                $key = (string)$section['key'];
                                $renderCard($key, $section, $valueFor($section, $values, $errors), $errors[$key] ?? []);
                                ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="sticky bottom-0 z-20 -mx-4 mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 bg-white/95 px-4 py-4 shadow-theme-md backdrop-blur-sm sm:-mx-6 sm:px-6">
            <span data-unsaved hidden class="inline-flex items-center gap-2 text-sm font-medium text-amber-600">
                <span class="size-2 rounded-full bg-amber-500" aria-hidden="true"></span>
                Có thay đổi chưa lưu
            </span>
            <div class="flex flex-wrap items-center gap-3 md:ml-auto">
                <button type="submit"
                        class="inline-flex h-11 items-center justify-center gap-2 rounded-[10px] bg-brand-500 px-5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Lưu thay đổi
                </button>
                <a href="<?= e(url('admin/pages/' . $page)) ?>" data-cancel
                   class="inline-flex h-11 items-center justify-center rounded-[10px] border border-gray-300 bg-white px-5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden">
                    Hủy
                </a>
            </div>
        </div>
    </form>
</div>
