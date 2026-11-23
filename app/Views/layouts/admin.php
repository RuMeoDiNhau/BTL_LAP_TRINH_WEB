<?php
/** Admin layout: TailAdmin khung tạm, chỉnh menu/top bar cho phần #2. */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tên admin từ session nếu có; Auth hiện là stub.
$adminName = 'Admin';
foreach (['admin_name', 'user_name', 'name'] as $key) {
    if (!empty($_SESSION[$key]) && is_string($_SESSION[$key])) {
        $adminName = $_SESSION[$key];
        break;
    }
}
foreach (['admin', 'user'] as $bag) {
    if (isset($_SESSION[$bag]) && is_array($_SESSION[$bag])) {
        foreach (['name', 'full_name', 'username'] as $key) {
            if (!empty($_SESSION[$bag][$key]) && is_string($_SESSION[$bag][$key])) {
                $adminName = $_SESSION[$bag][$key];
                break 2;
            }
        }
    }
}
$initials = function_exists('mb_substr')
    ? mb_strtoupper(mb_substr($adminName, 0, 1))
    : strtoupper(substr($adminName, 0, 1));

// Controller vẫn tiếng Việt; map ở view để toàn bộ UI/admin hiển thị tiếng Anh.
$uiText = [
    'Danh sách FAQ'                 => 'FAQ List',
    'Thêm FAQ'                      => 'Add FAQ',
    'Sửa FAQ'                       => 'Edit FAQ',
    'Trang About'                   => 'About Page',
    'Trang FAQ (nội dung)'          => 'FAQ Page',
    'Đã thêm câu hỏi.'              => 'FAQ added successfully.',
    'Đã cập nhật câu hỏi.'          => 'FAQ updated successfully.',
    'Đã xóa câu hỏi.'               => 'FAQ deleted successfully.',
    'Đã lưu nội dung trang.'        => 'Page content saved.',
    'Không tìm thấy câu hỏi.'       => 'FAQ not found.',
    'Phiên không hợp lệ, vui lòng thử lại.' => 'Invalid session. Please try again.',
];
$pageName = $uiText[$title] ?? (string)$title;
$rawSuccess = flash_get('success');
$rawError = flash_get('error');
$flashSuccess = $uiText[$rawSuccess] ?? $rawSuccess;
$flashError = $uiText[$rawError] ?? $rawError;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageName) ?> | DECOR Admin</title>
    <link rel="icon" href="<?= e(asset('assets/admin/favicon.ico')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/admin/style.css')) ?>">
    <script src="<?= e(asset('assets/admin/bundle.js')) ?>" defer></script>
    <script src="<?= e(asset('assets/js/admin-faqs.js')) ?>" defer></script>
    <script src="<?= e(asset('assets/js/admin-pages.js')) ?>" defer></script>
</head>
<body class="bg-gray-50 font-outfit" x-data="{ sidebarToggle: false }" @keydown.escape.window="sidebarToggle = false">
<!-- TailAdmin frame: sidebar + header + content. No charts/global search/bell/dark mode. -->
<div class="flex h-screen overflow-hidden">
    <div x-cloak x-show="sidebarToggle" aria-hidden="true" @click="sidebarToggle = false"
         class="fixed inset-0 z-99 bg-gray-900/50 backdrop-blur-xs xl:hidden"></div>

    <aside id="admin-sidebar" x-cloak aria-label="Sidebar navigation"
           :class="sidebarToggle ? 'translate-x-0' : '-translate-x-full'"
           class="sidebar fixed top-0 left-0 z-9999 flex h-screen w-72.5 flex-col border-r border-gray-200 bg-white px-5 transition-all duration-300 xl:static xl:translate-x-0"
           @click.outside="if (window.innerWidth < 1280) sidebarToggle = false">
        <div class="sidebar-header flex items-center gap-2 pt-5 pb-8">
            <a href="<?= e(url('admin/faqs')) ?>" class="flex items-center gap-2">
                <img class="size-9" src="<?= e(asset('assets/admin/images/logo/logo-icon.svg')) ?>" alt="">
                <span class="text-lg font-semibold text-gray-800">DECOR Admin</span>
            </a>
        </div>

        <div class="no-scrollbar flex flex-1 flex-col overflow-y-auto duration-300 ease-linear">
            <nav aria-label="Content menu">
                <h3 class="mb-4 text-xs leading-5 text-gray-400 uppercase">
                    <span class="menu-group-title">Content</span>
                </h3>
                <ul class="space-y-1">
                    <?php
                    // $active do controller truyền vào; $isActive quyết định class nguyên văn cho Tailwind.
                    $menu = [
                        ['key' => 'admin-about', 'label' => 'About Page', 'href' => url('admin/pages/about'), 'path' => 'M7 3h10a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm1 5h8M8 12h8M8 16h5'],
                        ['key' => 'admin-faq-page', 'label' => 'FAQ Page', 'href' => url('admin/pages/faq'), 'path' => 'M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H9l-5 4V5Zm4 4h8M8 12h5'],
                        ['key' => 'admin-faqs', 'label' => 'FAQ List', 'href' => url('admin/faqs'), 'path' => 'M4 6h16M4 12h16M4 18h10'],
                    ];
                    foreach ($menu as $item):
                        $isActive = ($active ?? '') === $item['key'];
                    ?>
                    <li>
                        <a href="<?= e($item['href']) ?>"
                           <?= $isActive ? 'aria-current="page"' : '' ?>
                           class="menu-item <?= $isActive ? 'menu-item-active' : 'menu-item-inactive' ?>">
                            <svg class="size-5 shrink-0 <?= $isActive ? 'menu-item-icon-active' : 'menu-item-icon-inactive' ?>"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="<?= e($item['path']) ?>"/>
                            </svg>
                            <span class="menu-item-text"><?= e($item['label']) ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>

        <div class="mt-auto border-t border-gray-100 pt-4 pb-5">
            <a href="<?= e(url('')) ?>" target="_blank" rel="noopener" class="menu-item menu-item-inactive">
                <svg class="size-5 shrink-0 menu-item-icon-inactive" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4M14 4h6v6M21 3l-9 9"/>
                </svg>
                <span class="menu-item-text">View website</span>
            </a>
        </div>
    </aside>

    <div class="relative flex flex-1 flex-col overflow-x-hidden overflow-y-auto">
        <main id="main-content">
            <header class="sticky top-0 z-50 flex w-full shrink-0 items-center justify-between border-b border-gray-200 bg-white px-4 py-3 sm:px-6">
                <div class="flex items-center gap-3">
                    <button type="button" @click.stop="sidebarToggle = !sidebarToggle" aria-label="Toggle menu"
                            aria-controls="admin-sidebar" :aria-expanded="sidebarToggle.toString()"
                            class="inline-flex size-10 items-center justify-center rounded-[10px] border border-gray-300 bg-white text-gray-600 shadow-theme-xs transition hover:bg-gray-50 hover:text-gray-900 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden active:translate-y-px xl:hidden">
                        <svg class="stroke-current" width="22" height="22" viewBox="0 0 24 24" fill="none"
                             stroke-width="1.5" stroke-linecap="round" aria-hidden="true">
                            <path d="M4 7h16M4 12h16M4 17h16"/>
                        </svg>
                    </button>
                    <span class="text-base font-semibold text-gray-800"><?= e($pageName) ?></span>
                </div>
                <div class="flex items-center gap-3">
                    <span aria-hidden="true"
                          class="flex size-9 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700"><?= e($initials) ?></span>
                    <span class="hidden text-sm font-medium text-gray-700 sm:block"><?= e($adminName) ?></span>
                </div>
            </header>

            <div class="mx-auto w-full max-w-6xl p-4 pb-20 md:p-6 md:pb-6">
                <?php if ($flashSuccess !== ''): ?>
                    <div x-data="{ visible: true }" x-show="visible" x-transition.opacity
                         x-init="setTimeout(function () { visible = false; }, 5000)"
                         class="mb-6 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800"
                         role="status">
                        <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/>
                        </svg>
                        <span class="flex-1"><?= e($flashSuccess) ?></span>
                        <button type="button" @click="visible = false" aria-label="Dismiss notification"
                                class="rounded-lg p-1 text-green-700 transition hover:bg-green-100 focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:outline-hidden">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 aria-hidden="true"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
                        </button>
                    </div>
                <?php endif; ?>
                <?php if ($flashError !== ''): ?>
                    <div x-data="{ visible: true }" x-show="visible" x-transition.opacity
                         class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
                         role="alert">
                        <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v5m0 3h.01M10.3 3.8 2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.8a2 2 0 0 0-3.4 0Z"/>
                        </svg>
                        <span class="flex-1"><?= e($flashError) ?></span>
                        <button type="button" @click="visible = false" aria-label="Dismiss notification"
                                class="rounded-lg p-1 text-red-700 transition hover:bg-red-100 focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:outline-hidden">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 aria-hidden="true"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
                        </button>
                    </div>
                <?php endif; ?>
                <?= $content ?>
            </div>
        </main>
    </div>
</div>
</body>
</html>
