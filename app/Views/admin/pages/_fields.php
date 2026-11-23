<?php
/** Một field nhập của khối; dùng chung cho mọi tab. Biến: $key, $section, $val, $err, $tr. */
$hasTitle = in_array('title', $section['fields'] ?? [], true);
$hasContent = in_array('content', $section['fields'] ?? [], true);
$isSeo = $key === 'seo';
$titleLength = function_exists('mb_strlen') ? mb_strlen((string)($val['title'] ?? ''), 'UTF-8') : strlen((string)($val['title'] ?? ''));
$contentLength = function_exists('mb_strlen') ? mb_strlen((string)($val['content'] ?? ''), 'UTF-8') : strlen((string)($val['content'] ?? ''));
?>
<?php if ($hasTitle): ?>
    <div>
        <div class="mb-1.5 flex flex-wrap items-center justify-between gap-2">
            <label for="<?= e($key) ?>_title" class="block text-sm font-medium text-gray-700">
                Title <span class="text-red-500">*</span>
            </label>
            <?php if ($isSeo): ?>
                <span class="text-xs <?= $titleLength > 60 ? 'text-red-600' : 'text-gray-400' ?>"
                      data-counter data-target="<?= e($key) ?>_title" data-limit="60"><?= (int)$titleLength ?> / 60</span>
            <?php endif; ?>
        </div>
        <input id="<?= e($key) ?>_title" name="sections[<?= e($key) ?>][title]" type="text" maxlength="255"
               value="<?= e((string)($val['title'] ?? '')) ?>"
               aria-invalid="<?= isset($err['title']) ? 'true' : 'false' ?>"
               class="h-11 w-full min-w-0 rounded-[10px] border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/20 focus:outline-hidden">
        <p class="mt-1.5 text-xs text-red-600" data-error="<?= e($key) ?>.title"><?= e($tr($err['title'] ?? '')) ?></p>
    </div>
<?php endif; ?>

<?php if ($hasContent): ?>
    <div>
        <div class="mb-1.5 flex flex-wrap items-center justify-between gap-2">
            <label for="<?= e($key) ?>_content" class="block text-sm font-medium text-gray-700">
                Content <span class="text-red-500">*</span>
            </label>
            <?php if ($isSeo): ?>
                <span class="text-xs <?= $contentLength > 160 ? 'text-red-600' : 'text-gray-400' ?>"
                      data-counter data-target="<?= e($key) ?>_content" data-limit="160"><?= (int)$contentLength ?> / 160</span>
            <?php endif; ?>
        </div>
        <textarea id="<?= e($key) ?>_content" name="sections[<?= e($key) ?>][content]"
                  rows="<?= $isSeo ? 5 : 4 ?>" maxlength="5000"
                  aria-invalid="<?= isset($err['content']) ? 'true' : 'false' ?>"
                  class="w-full rounded-[10px] border border-gray-300 bg-white px-4 py-3 text-sm shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/20 focus:outline-hidden"><?= e((string)($val['content'] ?? '')) ?></textarea>
        <p class="mt-1.5 text-xs text-red-600" data-error="<?= e($key) ?>.content"><?= e($tr($err['content'] ?? '')) ?></p>
    </div>
<?php endif; ?>
