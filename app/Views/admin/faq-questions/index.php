<?php
/** Customer Questions: search/filter, table, pagination, empty state, delete dialog. */
$pageLink = static function (int $number) use ($keyword, $status): string {
    $query = http_build_query(['q' => $keyword, 'status' => $status, 'page' => $number]);

    return e(url('admin/faq-questions') . '?' . $query);
};
$from = $total === 0 ? 0 : ($page - 1) * $perPage + 1;
$to = min($page * $perPage, $total);
?>
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Customer Questions</h1>
            <p class="mt-1 text-sm text-gray-500">Questions sent from the FAQ page.</p>
        </div>
        <span class="rounded-lg bg-white px-3 py-1.5 text-sm font-medium text-gray-600 shadow-theme-xs"><?= (int)$total ?> total</span>
    </div>

    <section aria-label="Question filters" class="rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-xs sm:p-6">
        <form method="get" action="<?= e(url('admin/faq-questions')) ?>" class="flex flex-col gap-4 md:flex-row md:items-center">
            <div class="relative w-full md:max-w-sm">
                <label for="questionSearch" class="sr-only">Search</label>
                <svg class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-gray-400"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-4-4"/>
                </svg>
                <input id="questionSearch" type="search" name="q" value="<?= e($keyword) ?>"
                       placeholder="Search name, email, subject or message"
                       class="h-11 w-full min-w-0 rounded-[10px] border border-gray-300 bg-white py-2.5 pr-4 pl-11 text-sm shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/20 focus:outline-hidden">
            </div>
            <label for="questionStatus" class="sr-only">Status</label>
            <select id="questionStatus" name="status"
                    class="h-11 w-full rounded-[10px] border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/20 focus:outline-hidden md:w-44">
                <option value="all"<?= $status === 'all' ? ' selected' : '' ?>>All</option>
                <option value="unread"<?= $status === 'unread' ? ' selected' : '' ?>>Unread</option>
                <option value="read"<?= $status === 'read' ? ' selected' : '' ?>>Read</option>
                <option value="replied"<?= $status === 'replied' ? ' selected' : '' ?>>Replied</option>
            </select>
            <button type="submit"
                    class="inline-flex h-11 shrink-0 items-center justify-center gap-2 rounded-[10px] border border-gray-300 bg-white px-5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-4-4"/>
                </svg>
                Search
            </button>
        </form>
    </section>

    <section aria-label="Question table" class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs">
        <?php if ($rows === []): ?>
            <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                <span class="flex size-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                    <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="14" rx="2"/><path stroke-linecap="round" d="m3 7 9 6 9-6"/>
                    </svg>
                </span>
                <h2 class="mt-4 text-base font-medium text-gray-800">No questions found</h2>
                <p class="mt-1 text-sm text-gray-500">Try another keyword or status filter.</p>
            </div>
        <?php else: ?>
            <div class="w-full overflow-x-auto">
                <div class="min-w-200">
                <table class="min-w-full">
                    <thead class="border-b border-gray-100 bg-gray-50/75">
                        <tr>
                            <th scope="col" class="w-14 px-4 py-3 text-center text-xs font-medium text-gray-500 sm:px-6">#</th>
                            <th scope="col" class="px-4 py-3 text-start text-xs font-medium text-gray-500 sm:px-6">Name</th>
                            <th scope="col" class="px-4 py-3 text-start text-xs font-medium text-gray-500 sm:px-6">Email</th>
                            <th scope="col" class="px-4 py-3 text-start text-xs font-medium text-gray-500 sm:px-6">Phone</th>
                            <th scope="col" class="px-4 py-3 text-start text-xs font-medium text-gray-500 sm:px-6">Subject</th>
                            <th scope="col" class="px-4 py-3 text-start text-xs font-medium text-gray-500 sm:px-6">Message</th>
                            <th scope="col" class="px-4 py-3 text-start text-xs font-medium text-gray-500 sm:px-6">Status</th>
                            <th scope="col" class="px-4 py-3 text-start text-xs font-medium text-gray-500 sm:px-6">Received</th>
                            <th scope="col" class="px-4 py-3 text-end text-xs font-medium text-gray-500 sm:px-6">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($rows as $index => $row): ?>
                            <?php if ($row['status'] === 'unread'): ?>
                            <tr class="font-semibold transition hover:bg-gray-50/50">
                            <?php else: ?>
                            <tr class="transition hover:bg-gray-50/50">
                            <?php endif; ?>
                                <td class="px-4 py-4 text-center text-sm text-gray-500 sm:px-6"><?= (int)(($page - 1) * $perPage + $index + 1) ?></td>
                                <td class="px-4 py-4 text-sm text-gray-800 sm:px-6"><?= e((string)$row['name']) ?></td>
                                <td class="px-4 py-4 text-sm text-gray-600 sm:px-6"><?= e((string)$row['email']) ?></td>
                                <td class="px-4 py-4 text-sm text-gray-600 sm:px-6"><?= e((string)($row['phone'] ?? '')) ?></td>
                                <td class="px-4 py-4 text-sm text-gray-800 sm:px-6"><?= e((string)$row['subject']) ?></td>
                                <td class="px-4 py-4 text-sm text-gray-600 sm:px-6">
                                    <span class="line-clamp-2"><?= e((string)$row['message']) ?></span>
                                </td>
                                <td class="px-4 py-4 sm:px-6">
                                    <?php if ($row['status'] === 'unread'): ?>
                                        <span class="inline-flex h-6 w-fit shrink-0 items-center justify-center gap-1 rounded-lg bg-amber-500 px-2.5 py-0.5 text-sm font-medium whitespace-nowrap text-white">Unread</span>
                                    <?php elseif ($row['status'] === 'read'): ?>
                                        <span class="inline-flex h-6 w-fit shrink-0 items-center justify-center gap-1 rounded-lg bg-gray-500 px-2.5 py-0.5 text-sm font-medium whitespace-nowrap text-white">Read</span>
                                    <?php else: ?>
                                        <span class="inline-flex h-6 w-fit shrink-0 items-center justify-center gap-1 rounded-lg bg-green-500 px-2.5 py-0.5 text-sm font-medium whitespace-nowrap text-white">Replied</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap sm:px-6"><?= e((string)$row['created_at']) ?></td>
                                <td class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="<?= e(url('admin/faq-questions/' . (int)$row['id'])) ?>"
                                           title="View question" aria-label="View question"
                                           class="inline-flex size-9 items-center justify-center rounded-lg text-brand-600 transition hover:bg-brand-50 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden">
                                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                 stroke-width="1.7" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12S6 6 12 6s9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Zm9.5 2.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                                            </svg>
                                        </a>
                                        <button type="button" title="Delete question" aria-label="Delete question"
                                                data-question-delete="<?= (int)$row['id'] ?>"
                                                data-question-subject="<?= e((string)$row['subject']) ?>"
                                                data-question-action="<?= e(url('admin/faq-questions/delete')) ?>"
                                                class="inline-flex size-9 items-center justify-center rounded-lg text-red-600 transition hover:bg-red-50 focus-visible:ring-3 focus-visible:ring-red-500/20 focus-visible:outline-hidden">
                                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                 stroke-width="1.7" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>
        <?php endif; ?>

        <nav aria-label="Question pagination"
             class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 px-4 py-4 sm:px-6">
            <span class="text-sm text-gray-500">
                Showing <span class="font-medium text-gray-700"><?= $from ?>–<?= $to ?></span> of <span class="font-medium text-gray-700"><?= (int)$total ?></span>
            </span>
            <div class="flex items-center gap-1.5">
                <?php if ($page > 1): ?>
                    <a href="<?= $pageLink($page - 1) ?>" aria-label="Previous page" title="Previous page"
                       class="inline-flex size-10 items-center justify-center rounded-[10px] border border-gray-300 bg-white text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15 6-6 6 6 6"/>
                        </svg>
                    </a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i === $page): ?>
                        <a href="<?= $pageLink($i) ?>" aria-current="page" aria-label="Page <?= $i ?>"
                           class="inline-flex size-10 items-center justify-center rounded-[10px] border border-transparent bg-brand-500 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden"><?= $i ?></a>
                    <?php else: ?>
                        <a href="<?= $pageLink($i) ?>" aria-label="Page <?= $i ?>"
                           class="inline-flex size-10 items-center justify-center rounded-[10px] border border-gray-300 bg-white text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="<?= $pageLink($page + 1) ?>" aria-label="Next page" title="Next page"
                       class="inline-flex size-10 items-center justify-center rounded-[10px] border border-gray-300 bg-white text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6"/>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </nav>
    </section>
</div>

<dialog id="questionDeleteDialog" aria-labelledby="questionDeleteTitle"
        class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xl">
    <div class="flex items-start justify-between gap-4">
        <span class="flex size-11 items-center justify-center rounded-full bg-red-50 text-red-600">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"/>
            </svg>
        </span>
        <button type="button" data-question-dialog-close aria-label="Close dialog"
                class="inline-flex size-9 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/>
            </svg>
        </button>
    </div>
    <h2 id="questionDeleteTitle" class="mt-4 text-lg font-semibold text-gray-800">Delete question</h2>
    <p class="mt-2 text-sm text-gray-500">This will permanently remove the following question. This action cannot be undone.</p>
    <p data-question-subject class="mt-4 rounded-xl bg-gray-50 p-3 text-sm font-medium text-gray-800"></p>
    <form method="post" id="questionDeleteForm" action="#" class="mt-6 flex justify-end gap-3">
        <?= csrf_field() ?>
        <input type="hidden" name="id" id="questionDeleteId" value="">
        <button type="button" data-question-dialog-close data-question-dialog-cancel
                class="inline-flex h-11 items-center justify-center rounded-[10px] border border-gray-300 bg-white px-5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 focus-visible:ring-3 focus-visible:ring-brand-500/20 focus-visible:outline-hidden">
            Cancel
        </button>
        <button type="submit"
                class="inline-flex h-11 items-center justify-center rounded-[10px] bg-red-600 px-5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-red-700 focus-visible:ring-3 focus-visible:ring-red-500/20 focus-visible:outline-hidden">
            Delete
        </button>
    </form>
</dialog>
